<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateUserInfoRequest;
use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TestController extends Controller
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
        $form = Form::findOrFail($request->input('form_id'));
        $userInfo = $request->input('userInfo');

        FormAnswer::createFormFromRequest($request, $form, $userInfo);
        return response()->json(['messages' => 'Formulario diligenciado exitosamente. Serás redirigido a la página de inicio']);
    }

    public function validateUserInputData(ValidateUserInfoRequest $request)
    {
        return $request->validated();//parse data
    }

    public function startTest(Request $request)
    {
        $data = $request->all();

        $test = DB::table('forms')->first();
        return Inertia::render('Test/Show', ['test' => $test,
            'user' => ['userName' => $data['userName'], 'identification' => $data['identification'], 'age' => $data['age'], 'sex' => $data['sex']],
            'canSend' => true]);
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
    public function destroy($id)
    {
        //
    }
}
