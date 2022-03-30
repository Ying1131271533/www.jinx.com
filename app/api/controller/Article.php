<?php
declare (strict_types = 1);

namespace app\api\controller;

// use app\api\middleware\CheckParams;
use app\api\exception\Fail;
use app\api\exception\Success;
use app\common\model\Article as A;
use app\common\model\User as U;

class Article extends Base
{
    /**
     * 新增文章
     *
     * @param  int       $user_id    用户id
     * @param  string    $title      文章标题
     * @return json                  api返回的json数据
     */
    public function save()
    {
        try {
            // 接收参数
            $data = $this->params;
            // 开启事务
            U::startTrans();

            // 写入数据库
            $user = U::find($data['user_id']);
            empty($user) and $this->create(400, '找不到该用户');
            $article = $user->articles()->save($data);
            !empty($data['content']) and $article->desc()->save($data);

            // 提交事务
            U::commit();
            throw new Success(['msg' => '文章新增成功', 'data' => ['id' => $article['id']]]);
        } catch (\Exception $e) {
            // 回滚事务
            U::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 查看文章列表
     *
     * @param  int       $user_id    用户id
     * @param  int       $number     每页个数
     * @param  int       $page       页码
     * @param  string    $title      标题
     * @param  string    $content    内容
     * @return json                  api返回的json数据
     */
    public function index()
    {
        try {
            // 接收参数
            $data = $this->params;

            // 是否有文章条数和页码的参数
            empty($data['number']) and $data['number'] = config('app.number');
            empty($data['page']) and $data['page']     = config('app.page');

            // 查询数据库
            $user     = U::find($data['user_id']);
            $count    = $user->articles()->count();
            $articles = $user->articles()
                ->where('is_del', 0)
                ->field('id, title, create_time')
                ->page($data['page'], $data['number'])
                ->order('id', 'desc')
                ->select();

            // 总页数
            $page_num = ceil($count / $data['number']);

            // 判断是否有数据
            if ($articles->isEmpty()) {
                $this->return_msg(204, HTTP_EMPTY_MSG);
            }

            // 返回api接口
            $resultData = [
                'articles' => $articles,
                'page_num' => $page_num,
                'author'   => $user['nickname'],
            ];
            $this->return_msg(200, HTTP_SUCCESS_MSG, $resultData);
        } catch (\Exception $e) {
            $this->return_msg(400, HTTP_ERROR_MSG);
        }
    }

    /**
     * 查看单个文章信息
     *
     * @param  int       $id        文章id
     * @return json                 api返回的json数据
     */
    public function read()
    {
        // 接收参数
        $data = $this->params;
        // 找出该文章
        // $article = A::cache(true)->find($data['id']);
        $article = A::withJoin(['user' => ['nickname'], 'desc' => ['content']])->cache('article_user_desc|' . $data['id'])->find($data['id']);
        // $article = A::withJoin(['user', 'desc'])->cache(true)->find($data['id']);
        // halt($article);
        if (empty($article)) {
            throw new Miss();
        }
        
        // 返回api接口数据
        $resultData = [
            'id'          => $article['id'],
            'title'       => $article['title'],
            'create_time' => $article['create_time'],
            'nickname'    => $article['user']['nickname'],
            // 'nickname'    => $article->user()->cache(true)->value('nickname'),
            'content'     => $article['desc']['content'],
            // 'content'     => $article->desc()->cache(true)->value('content'),
        ];
        throw new Success(['data' => $resultData]);
    }

    /**
     * 修改/保存文章
     *
     * @param  int       $id        文章id
     * @return json                 api返回的json数据
     */
    public function update()
    {
        // 接收参数
        $data = $this->params;
        // 开启事务
        A::startTrans();
        // 更新数据库
        $article = A::cache('article|' . $data['id'])->update($data);
        if (!$article) {
            // 回滚事务
            A::rollback();
            throw new Fail(['msg' => '修改失败', 'code' => 408]);
        }

        !empty($data['content']) and $article->desc()->save($data);

        // 提交事务
        A::commit();
        throw new Success(['msg' => '文章更新成功', 'data' => ['id' => $article['id']]]);
    }

    /**
     * 删除文章
     *
     * @param  int       $id        文章id
     * @return json                 api返回的json数据
     */
    public function delete()
    {
        try {
            // 接收参数
            $data = $this->params;

            // 删除数据(逻辑删除)
            $result = A::where('id', $data['id'])->update(['is_del' => 1]);
            empty($result) and $this->return_msg(400, '文章删除失败');

            // 删除数据(物理删除，删库跑路)
            // A::startTrans();
            // AD::where('article_id', $data['id'])->delete();
            // $result = A::destroy($data['id']);
            // if (!$result) {
            // $this->return_msg(400, '文章删除失败');
            // }

            // // 提交事务
            // A::commit();

            $this->return_msg(200, HTTP_SUCCESS_MSG);
        } catch (\Exception $e) {
            // 回滚事务
            // A::rollback();
            $this->return_msg(400, HTTP_FAIL_MSG);
        }
    }
}
