<?php

namespace App\Http\Controllers;

use App\Models\AcademicArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AcademicAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $academicAreaId = $request->input('academicArea');
        if($academicAreaId !== null){
            return response()->json(DB::table('academic_programs')->where('academic_area_id','=',$academicAreaId)->get());
        }
        return response()->json(AcademicArea::getAcademicAreas());
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
        AcademicArea::create($request->all());
        return response()->json(['message' => 'Área creada exitosamente']);

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
    public function edit(AcademicArea $academicArea)
    {
        return Inertia::render('AcademicAreas/Edit', ['academicArea' => $academicArea]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicArea $academicArea)
    {
        $academicArea->update($request->all());
        return response()->json(['message' => 'Área actualizada exitosamente']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
