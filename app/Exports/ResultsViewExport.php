<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ResultsViewExport implements FromView
{

    private $questions;

    private $rows;

    public function __construct($questions, $rows)
    {
        $this->questions = $questions;
        $this->rows = $rows;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view ('report', ['questions' => $this->questions, 'tableData' => $this->rows]);
    }
}
