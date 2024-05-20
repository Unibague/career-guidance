<?php

namespace App\Http\Controllers;

use App\Models\AcademicProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicAreaProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function assign(Request $request)
    {
        $academicProgramCode = $request->input('academicProgram');
        $academicArea = $request->input('academicArea');
        DB::table('academic_programs')->where('code','=',$academicProgramCode)->update(['academic_area_id'=> $academicArea['id']]);
        return response()->json(['message' => 'Programa vinculado correctamente al area']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicProgram $academicProgram)
    {
        dd($academicProgram);
    }

    public function delete(Request $request)
    {
        $academicProgram = $request->input('academicAreaProgram');
        DB::table('academic_programs')->where('code','=',$academicProgram)->update(['academic_area_id'=>null]);
        return response()->json(['message' => 'Programa desvinculado del area correctamente']);
    }

}
