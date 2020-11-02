<?php

namespace App\Jobs;

use App\Models\Producto;
use App\Models\Venta;
use App\Notifications\FailedGiftCardMailNotification;
use App\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $venta;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($venta)
    {
        $this->venta = $venta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('handle GenerateVoucher: ' . $this->venta->id);

        $pdfs = $this->venta->generatePdfs();

        foreach ($pdfs as $key => $elem) {

            // Save PDF's on disk
            Storage::disk('pdfs')->put($this->venta->id . '/' . $elem['pdf_filename'], $elem['pdf']->output());
        }

        $zip = new \ZipArchive();

        // Create zip File
        $fileName = 'voucher' . $this->venta->id . '.zip';

        if ($zip->open(storage_path($fileName), \ZipArchive::CREATE) === TRUE)
        {
            \Log::info('zip crewado');
            // Add each PDF to ZIP file
            $files = \File::files(Storage::disk('pdfs')->path($this->venta->id . '/' ));
            foreach ($files as $key => $value) {

                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();
        }
        else
        {
            \Log::info('no se puedo');
        }

        // Delete innecesary pdf files
        Storage::disk('pdfs')->deleteDirectory($this->venta->id);
    }

    public function failed(\Exception $e)
    {
        \Log::info('failed GenerateVoucher: ' . $this->venta->id);
        User::find(1)->notify(new FailedGiftCardMailNotification($this->venta, $e));
    }
}
