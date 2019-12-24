<?php
namespace catchAdmin\user\controller;

use app\Request;
use catchAdmin\permissions\model\Roles;
use catchAdmin\user\Auth;
use catchAdmin\user\model\Users;
use catchAdmin\user\request\CreateRequest;
use catchAdmin\user\request\UpdateRequest;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\Tree;

class User extends CatchController
{
    protected $user;

    public function __construct(Users $user)
    {
       $this->user = $user;
    }

    /**
     *
     * @time 2019年12月04日
     * @param Request $request
     * @return string
     * @throws \think\db\exception\DbException
     */
    public function index(Request $request)
    {
        return CatchResponse::paginate($this->user->getList($request->param()));
    }

    public function info()
    {
        return CatchResponse::success(Auth::getUserInfo());
    }

    /**
     *
     * @time 2019年12月06日
     * @throws \Exception
     * @return string
     */
    public function create()
    {}

    /**
     *
     * @param CreateRequest $request
     * @time 2019年12月06日
     * @return \think\response\Json
     */
    public function save(CreateRequest $request)
    {
        $this->user->storeBy($request->post());

        if (!empty($request->param('roleids'))) {
            $this->user->attach($request->param('roleids'));
        }

        return CatchResponse::success('', '添加成功');
    }

    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @return \think\response\Json
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
    public function edit($id){}
    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @param UpdateRequest $request
     * @return \think\response\Json
     */
    public function update($id, UpdateRequest $request)
    {
        $this->user->updateBy($id, $request->post());

        $user = $this->user->findBy($id);

        $user->detach();

        if (!empty($request->param('roleids'))) {
            $user->attach($request->param('roleids'));
        }

        return CatchResponse::success();
    }

    /**
     *
     * @time 2019年12月04日
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        // 删除角色
        $this->user->findBy($id)->detach();

        $this->user->deleteBy($id);

        return CatchResponse::success();
    }

    /**
     *
     * @time 2019年12月07日
     * @param $id
     * @return \think\response\Json
     */
    public function switchStatus($id): \think\response\Json
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
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function recover($id): \think\response\Json
    {
       $trashedUser = $this->user->findBy($id, ['*'], true);

       if ($this->user->where('email', $trashedUser->email)->find()) {
           return CatchResponse::fail(sprintf('该恢复用户的邮箱 [%s] 已被占用', $trashedUser->email));
       }

       return CatchResponse::success($this->user->recover($id));
    }

    /**
     *
     * @time 2019年12月11日
     * @param Request $request
     * @param Roles $roles
     * @return \think\response\Json
     */
    public function getRoles(Request $request, Roles $roles): \think\response\Json
    {
        $roles = Tree::done($roles->getList());

        $roleIds = [];
        if ($request->param('uid')) {
            $userHasRoles = $this->user->findBy($request->param('uid'))->getRoles();
            foreach ($userHasRoles as $role) {
                $roleIds[] = $role->pivot->role_id;
            }
        }

        return CatchResponse::success([
            'roles' => $roles,
            'hasRoles' => $roleIds,
        ]);
    }
}
