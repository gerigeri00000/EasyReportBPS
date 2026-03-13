<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Submission;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Shared\Converter;
use Illuminate\Support\Facades\Storage;

class WordTemplateService
{
    /**
     * Path to the Word template .docx file.
     *
     * @var string
     */
    protected $templatePath;

    /**
     * Create a new instance.
     *
     * @param string $templatePath Path to the .docx template
     */
    public function __construct($templatePath = null)
    {
        if (!$templatePath) {
            // Default template location
            $templatePath = resource_path('templates/report_template.docx');
        }
        $this->templatePath = $templatePath;
    }

    /**
     * Get the template path.
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Generate a Word document for an activity with all its submissions.
     *
     * @param Activity $activity
     * @return string Path to the generated temporary file
     * @throws \Exception If template not found
     */
    public function generateForActivity(Activity $activity)
    {
        // Load the template
        if (!file_exists($this->templatePath)) {
            $msg = "Word template not found at: {$this->templatePath}\n\n";
            $msg .= "Please create the template file with the following placeholders:\n";
            $msg .= "- Activity placeholders: \${nama_kegiatan}, \${kecamatan}, \${desa}, \${nks_1}, \${nks_2}, \${nks_3}, \${sls_1}, \${sls_2}, \${sls_3}, \${nama_pemeriksa}\n";
            $msg .= "- A block named 'submission' that contains: \${nama_mitra}, and sample placeholders: \${nama_sampel_1_1} ... \${nama_sampel_5_2} and \${foto_sampel_1_1} ... \${foto_sampel_5_2}\n";
            $msg .= "- Arrange photo placeholders in a 5x2 table (5 rows, 2 columns) and resize images to 5cm in the template.\n";
            throw new \Exception($msg);
        }

        $template = new TemplateProcessor($this->templatePath);

        // Set activity-level placeholders (single values)
        $template->setValue('nama_kegiatan', $activity->title);
        $template->setValue('kecamatan', $activity->kecamatan ?? '');
        $template->setValue('desa', $activity->desa ?? '');
        $template->setValue('nks_1', $activity->nks_1 ?? '');
        $template->setValue('nks_2', $activity->nks_2 ?? '');
        $template->setValue('nks_3', $activity->nks_3 ?? '');
        $template->setValue('sls_1', $activity->sls_1 ?? '');
        $template->setValue('sls_2', $activity->sls_2 ?? '');
        $template->setValue('sls_3', $activity->sls_3 ?? '');
        $template->setValue('nama_pemeriksa', $activity->nama_pemeriksa ?? '');

        // Get all submissions for this activity, with samples
        $submissions = $activity->submissions()->with('samples')->orderBy('created_at', 'asc')->get();

        // Prepare data blocks for each submission
        $submissionBlocks = [];
        foreach ($submissions as $submission) {
            $block = [
                'nama_mitra' => $submission->nama_mitra,
            ];

            // Initialize all 10 sample name and photo placeholders for 5x2 table
            // The template expects placeholders: nama_sampel_1_1 ... nama_sampel_5_2
            // and foto_sampel_1_1 ... foto_sampel_5_2
            for ($i = 1; $i <= 5; $i++) {
                for ($j = 1; $j <= 2; $j++) {
                    $nameKey = "nama_sampel_{$i}_{$j}";
                    $photoKey = "foto_sampel_{$i}_{$j}";
                    $block[$nameKey] = '';
                    $block[$photoKey] = null; // null means no image
                }
            }

            // Fill in actual samples (up to 5 respondents, 2 samples each)
            foreach ($submission->samples as $sample) {
                $i = $sample->respondent_index;
                $j = $sample->sample_index;
                if ($i >= 1 && $i <= 5 && $j >= 1 && $j <= 2) {
                    $nameKey = "nama_sampel_{$i}_{$j}";
                    $block[$nameKey] = $sample->name;
                    if ($sample->photo_path) {
                        // Get absolute path to the image
                        $photoFullPath = storage_path('app/public/' . $sample->photo_path);
                        if (file_exists($photoFullPath)) {
                            // Resize image to 5cm (both width and height, preserve ratio)
                            $block["foto_sampel_{$i}_{$j}"] = [
                                'src'    => $photoFullPath,
                                'width'  => Converter::cmToPixel(5), // Lebar tetap 5cm
                                'height' => null,                    // Tinggi otomatis (mengikuti rasio)
                                'ratio'  => true                     // Memastikan rasio tetap terjaga
                            ];
                        }
                    }
                }
            }

            $submissionBlocks[] = $block;
        }

        // If there are no submissions, we still need at least one empty block to show the template layout?
        // The template might have a block for one submission that we can clone zero times? We'll just not clone if none.
        if (count($submissionBlocks) > 0) {
            // 1. Clone bloknya dulu
            // Parameter: blockname, count, replace, limit
            $template->cloneBlock('submission', count($submissionBlocks), true, true);

            // 2. Loop data untuk mengisi tiap blok yang sudah di-clone
            foreach ($submissionBlocks as $index => $data) {
                $blockNum = $index + 1; // Index dimulai dari 1
                $suffix = '#' . $blockNum;

                // Isi setiap field di dalam blok tersebut
                foreach ($data as $key => $value) {
                    if (is_array($value) && isset($value['src'])) {
                        // Jika data berupa array image (seperti yang kita buat tadi)
                        $template->setImageValue($key . $suffix, $value);
                    } else {
                        // Jika data berupa text biasa
                        $template->setValue($key . $suffix, (string)$value);
                    }
                }
            }
        } else {
            // Jika tidak ada data, hapus blok agar tidak muncul placeholder mentah
            $template->deleteBlock('submission');
        }
    }
}