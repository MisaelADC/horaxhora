<?php

namespace App\Exports;

use App\Models\Production;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;


class ProduccionDetallesTodoExport
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $turno;
    protected $tipo;

    public function __construct($fechaInicio, $fechaFin, $turno, $tipo)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->turno = $turno;
        $this->tipo = $tipo;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Turno');
        $sheet->setCellValue('C1', 'Máquina o mesa');
        $sheet->setCellValue('D1', 'Wo');
        $sheet->setCellValue('E1', 'Diseño');
        $sheet->setCellValue('F1', 'Empleado');
        $sheet->setCellValue('G1', 'Razón_Tm');
        $sheet->setCellValue('H1', 'Comienzo');
        $sheet->setCellValue('I1', 'Final');
        $sheet->setCellValue('J1', 'Tiempo');

        // Consulta con filtros
       $query = Production::with([
            'wo.product',
            'shift',
            'machine',
            'user',
            'downtimeReason'
        ])->whereIn('Status', [1, 3]);

        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween('Date', [$this->fechaInicio, $this->fechaFin]);
        }

        if ($this->turno) {
            $query->where('IdShift', $this->turno);
        }

        if ($this->tipo) {
            $query->whereHas('machine', function ($q) {
                $q->where('Tipo', $this->tipo);
            });
        }
        $productions = $query->get();

        $row = 2;

        foreach ($productions as $prod) {
            $sheet->setCellValue('A' . $row, $prod->Fecha ?? '');
            $sheet->setCellValue('B' . $row, $prod->shift->Shift ?? '');
            $sheet->setCellValue('C' . $row, $prod->machine->Machine ?? '');
            $sheet->setCellValue('D' . $row, $prod->wo->code ?? '');
            $sheet->setCellValue('E' . $row, $prod->wo->product->name ?? '');
            $sheet->setCellValue('F' . $row, $prod->user->name ?? '');
            $sheet->setCellValue('G' . $row, $prod->downtimeReason->reason ?? '');
            $sheet->setCellValue('H' . $row, $prod->comienzo ?? '');
            $sheet->setCellValue('I' . $row, $prod->final ?? '');
            $sheet->setCellValue('J' . $row, $prod->tiempo ?? '');

            $row++;
        }
        
        // Guardar archivo temporalmente y devolver como descarga
        $fileName = 'detalles_produccion_' . now()->format('Ymd_His') . '.xlsx';

        return Response::streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output'); // ← esto lo envía directo al navegador
        }, $fileName);
        $tempPath = storage_path("app/temp/{$fileName}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return Response::download($tempPath)->deleteFileAfterSend(true);
    }
}
