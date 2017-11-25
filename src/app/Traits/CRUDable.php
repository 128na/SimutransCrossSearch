<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Validator;

trait CRUDable {

  protected $view_name  = 'crudable';
  protected $title      = 'crudable';
  protected $model_name = '';
  protected $route      = '';
  protected $fields     = [];
  protected $options    = [];
  protected $validation = [];

  /**
   * 一覧
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $view_name = $this->view_name;
    $title     = $this->title;
    $models    = $this->model_name::all();
    $route     = $this->route;
    $fields    = $this->fields;
    $options   = $this->options;

    return view("{$this->view_name}.index",
      compact(
        'view_name',
        'title',
        'models',
        'route',
        'fields',
        'options'
      ));
  }

  /**
   * 新規作成
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), $this->validation['store']);
    if ($validator->fails()) {
      return redirect()
        ->route("{$this->route}.index")
        ->withInput()
        ->withErrors($validator, 'create');
    }

    $this->model_name::create($this->getInput($request));
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
    $validator = Validator::make($request->all(), $this->validation['update']);
    if ($validator->fails()) {
      return redirect()
        ->route("{$this->route}.index")
        ->withInput()
        ->withErrors($validator, "update.{$id}");
    }

    $model = $this->model_name::findOrFail($id);
    $model->fill($this->getInput($request))->save();

    $request->session()->flash('status', '更新しました');
    return redirect()->route("{$this->route}.index");
  }

  /**
   * 削除
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $this->model_name::destroy($id);

    $request->session()->flash('status', '削除しました');
    return redirect()->route("{$this->route}.index");
  }

  private function getInput(Request $request)
  {
    return $request->only(array_keys($this->fields));
  }
}
