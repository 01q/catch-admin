<?php
namespace catchAdmin\user\controller;

use app\Request;
use catchAdmin\user\model\Users;
use catchAdmin\user\request\CreateRequest;
use catchAdmin\user\request\UpdateRequest;
use catcher\base\BaseController;
use catcher\CatchForm;
use catcher\CatchResponse;
use think\response\Json;

class User extends BaseController
{
    protected $user;

    public function __construct(Users $user)
    {
       $this->user = $user;
    }

    /**
     *
     * @time 2019年12月04日
     * @throws \Exception
     * @return string
     */
    public function index()
    {
        return $this->fetch();
    }

    public function list(Request $request)
    {
        return CatchResponse::paginate($this->user->getList($request->param()));
    }

    /**
     *
     * @time 2019年12月06日
     * @throws \Exception
     * @return string
     */
    public function create()
    {
        $form = new CatchForm();

        $form->formId('userForm');
        $form->text('username', '用户名')->verify('required')->placeholder('请输入用户名');
        $form->text('email', '邮箱')->verify('email')->placeholder('请输入邮箱');
        $form->password('password', '密码')->id('pwd')->verify('required|psw')->placeholder('请输入密码');
        $form->password('passwordConfirm', '确认密码')->verify('required|equalTo', ['pwd', '两次密码输入不一致'])->placeholder('请再次输入密码');
        $form->formBtn('submitUser');

        return $this->fetch([
            'form' => $form->render(),
        ]);
    }

    /**
     *
     * @param CreateRequest $request
     * @time 2019年12月06日
     * @return Json
     */
    public function save(CreateRequest $request)
    {
        return CatchResponse::success($this->user->storeBy($request->post()));
    }

    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @return Json
     */
    public function read($id)
    {
        return CatchResponse::success($this->user->findBy($id));
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function edit($id)
    {
        $user = $this->user->findBy($id, ['id','username', 'email']);
        $form = new CatchForm();

        $form->formId('userForm');
        $form->text('username', '用户名')->verify('required')->default($user->username)->placeholder('请输入用户名');
        $form->text('email', '邮箱')->verify('email')->default($user->email)->placeholder('请输入邮箱');
        $form->password('password', '密码')->id('pwd')->placeholder('请输入密码');
        $form->password('passwordConfirm', '确认密码')->verify('equalTo', ['pwd', '两次密码输入不一致'])->placeholder('请再次输入密码');
        $form->formBtn('submitUser');

        return $this->fetch([
            'form' => $form->render(),
            'uid'  => $user->id,
        ]);
    }

    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @param UpdateRequest $request
     * @return Json
     */
    public function update($id, UpdateRequest $request)
    {
        return CatchResponse::success($this->user->updateBy($id, $request->post()));
    }

    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @return Json
     */
    public function delete($id)
    {
        return CatchResponse::success($this->user->deleteBy($id));
    }

    /**
     *
     * @time 2019年12月07日
     * @param $id
     * @return Json
     */
    public function switchStatus($id): Json
    {
        $user = $this->user->findBy($id);
        return CatchResponse::success($this->user->updateBy($id, [
            'status' => $user->status == Users::ENABLE ? Users::DISABLE : Users::ENABLE,
        ]));
    }

    /**
     *
     * @time 2019年12月07日
     * @param $id
     * @return Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function recover($id): Json
    {
       $trashedUser = $this->user->findBy($id, ['*'], true);

       if ($this->user->where('email', $trashedUser->email)->find()) {
           return CatchResponse::fail(sprintf('该恢复用户的邮箱 [%s] 已被占用', $trashedUser->email));
       }

       return CatchResponse::success($this->user->recover($id));
    }
}