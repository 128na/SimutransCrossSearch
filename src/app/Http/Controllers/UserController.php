<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Traits\CRUDable;

class UserController extends Controller
{
  use CRUDable;

  public function __construct()
  {
    $this->title      = 'Users';
    $this->model_name = User::class;
    $this->route      = 'user';
    $this->fields     = [
      'name'     => 'text',
      'email'    => 'text',
      'password' => 'password',
    ];
    $this->validation = [
      'store' => [
        'name'     => 'required|unique:users,name',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required',
      ],
      'update' => [
        'name'     => 'required',
        'email'    => 'required|email',
        'password' => 'required',
      ],
    ];
  }

  /**
   * 新規作成
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), $this->validation);
    if ($validator->fails()) {
      return redirect()
        ->route("{$this->route}.index")
        ->withErrors($validator, 'create');
    }

    $this->model_name::create([
      'name'     => $request->input('name'),
      'email'    => $request->input('email'),
      'password' => bcrypt($request->input('password')),
    ]);
    $request->session()->flash('status', '作成しました');
    return redirect()->route("{$this->route}.index");
  }

  /**
   * 更新
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), $this->validation);
    if ($validator->fails()) {
      return redirect()
        ->route("{$this->route}.index")
        ->withErrors($validator, "update.{$id}");
    }

    $model = $this->model_name::findOrFail($id);
    $model->fill([
      'name'     => $request->input('name'),
      'email'    => $request->input('email'),
      'password' => bcrypt($request->input('password')),
    ])->save();

    $request->session()->flash('status', '更新しました');
    return redirect()->route("{$this->route}.index");
  }

}
