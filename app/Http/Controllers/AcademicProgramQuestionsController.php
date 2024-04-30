<?php

namespace App\Http\Controllers;

use App\Models\AcademicProgram;
use App\Models\AcademicProgramQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicProgramQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $academicProgramCode = $request->input('academicProgram');
        return response()->json(DB::table('academic_program_questions')->select(['id','academic_program_code','question as name'])->where('academic_program_code','=',$academicProgramCode)->get());
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
        $academicProgramQuestion = $request->all();
        DB::table('academic_program_questions')->updateOrInsert(
            ['academic_program_code' => $academicProgramQuestion["program_code"], 'question' => $academicProgramQuestion["name"]],
            ['question' => $academicProgramQuestion["name"]]);
        return response()->json(['message' => 'Pregunta añadida correctamente']);
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
    public function update(Request $request, AcademicProgramQuestion $academicProgramQuestion)
    {
        $data = $request->all();
        DB::table('academic_program_questions')->updateOrInsert(['id' => $data['id']] , ['question' => $data['name']]);
        return response()->json(['message' => 'Pregunta actualizada correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicProgramQuestion $academicProgramQuestion)
    {
        try{
            $academicProgramQuestion->delete();
        } catch (\Exception $exception){
            return response()->json(['message' => 'No puedes borrar una pregunta si esta ya ha sido contestada en algún formulario!'],400);
        }
        return response()->json(['message' => 'Pregunta borrada exitosamente']);
    }
}
