<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Content;
use app\model\Type;
use app\model\User;
use app\validate\ContentValidate;
use app\validate\UserValidate;
use think\exception\ValidateException;
use think\Request;

class Api extends BaseController
{

    /**
     * 获取用户下面的顶级type目录列表
     *
     * @param  \think\Request  $request
     * @return \think\Response
     *
     */
    public function getUserTopTypeList(Request $request)
    {
        $userInfo = User::find($this->user_id);
        $list = $userInfo->types()->where([['parent_id','=',getTopTypeId($this->user_id)],['status','=',1]])->select();

        $this->response(200,'获取成功',$list);

    }

    /**
     * 获取type子目录及下级子目录
     *
     * @param  \think\Request  $request
     * @return \think\Response
     *
     */
    public function getTypeSonLists(Request $request)
    {
        //默认返回树
        $userInfo = User::find($this->user_id);
        $list = $userInfo->types()->field('id,parent_id,parent_id as pId,title as name')->where([['parent_id','=',0],['status','=',1]])->select();
        if(!$list->isEmpty()){
            $list = $list->toArray();
            $new_list = [];
            foreach ($list as $k=>$v){
                $new_list = getSonTypes($this->user_id, $v['id'],$new_list);
            }
            $list = array_merge($list,$new_list);
        }else{
            $list = [];
        }
        $this->response(200,'获取成功', $list);
    }



    /**
     * 获取第二栏type子目录及文件列表
     *
     * @param  \think\Request  $request
     * @return \think\Response
     *
     */
    public function getSonTypeAndContentList(Request $request)
    {
        $arr = [];
        $parent_id = $request->param('parent_id',getTopTypeId($this->user_id));
        $list_type = Type::where([['parent_id','=',$parent_id],['user_id','=',$this->user_id],['status','=',1]])->select();
        if(!$list_type->isEmpty()){
            $arr['type'] = $list_type;
        }

        $list_content = Content::where([['type_id','=',$parent_id],['user_id','=',$this->user_id],['status','=',1]])->select();
        if(!$list_content->isEmpty()){
            $arr['content'] = $list_content;
        }

        $this->response(200,'获取成功', $arr);
    }


    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 4:59 下午
     * 获取内容接口
     */
    public function getContent(Request $request){
        try {
            validate(ContentValidate::class)->batch(true)->check([
                'id'=>$request->param('content_id')
            ]);
            $Content = new Content();
            $content_id = $request->param('content_id');
            $info =  $Content::where([['id', '=',$content_id],['status','=',1]])->findOrEmpty();
            if ($info->isEmpty()){
                response(101, '抱歉，笔记不存在');
            }else{
                response(200, '获取成功',$info);
            }

        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            foreach ($e->getError() as $v){
                response(102, $v);
            }

        }
    }

    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 5:45 下午
     * 编辑|添加 内容
     */
    public function updateContent(Request $request){


        $ret_arr = [];

        $data = [
            'content'=>$request->param('content'),
            'type_id'=>$request->param('type_id'),
            'title'=>$request->param('title')

        ];
        if (!$data['type_id']){
            return $this->response(106, '请选择文件夹');

        }else{
            $type_name = Type::where('id',$data['type_id'])->value('title');
            if($type_name == '回收站'){
                return $this->response(107, '回收站不能添加笔记');
            }
        }
        if ($request->param('id')){
            //修改
            $info = Content::where([['id','=',$request->param('id')],['user_id','=',$this->user_id]])->findOrEmpty();
            if($info->isEmpty()){
                return $this->response(101, '笔记不存在');
            }
            $info->content = $data['content'];
            $info->title = $data['title'];
            $info->type_id = $data['type_id'];
            $result = $info->force()->save();
            $ret_arr = [
                'title'=>$request->param('title')
            ];
        }else{



            if (!$request->param('title')){
                return $this->response(101, '请添加笔记标题');
            }
            $info = Content::where([['title','=',trim($request->param('title'))],['type_id','=',$request->param('type_id')],['user_id','=',$this->user_id]])->findOrEmpty();
            if (!$info->isEmpty()){
                return $this->response(105, '笔记已存在');
            }
            $data['user_id'] = $this->user_id;

            $content = new Content();
            $result = $content->save($data);
            $ret_arr = [
                'id'=>$content->id,
                'title'=>$request->param('title')
            ];
        }

        if ($result){
            return $this->response(200, '编辑成功',$ret_arr);
        }else{
            return $this->response(103, '编辑失败');
        }


    }

    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * 新建type
     */

    public function addType(Request $request){
        $param = $request->param();
        $info = Type::where([['title','=', $param['title']],['parent_id','=', $param['parent_id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            $param['user_id'] = $this->user_id;
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '添加成功',['id'=>$info->id]);
            }else{
                return $this->response(101, '添加失败');
            }
        }else{
            return $this->response(102, '文件夹已存在');
        }


    }

    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * 重命名type
     */

    public function updateType(Request $request){
        $param = $request->param();
        $info = Type::where([['id','=', $param['id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            return $this->response(102, '文件夹不存在');
        }else{
            $info->title = $param['title'];
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '编辑成功');
            }else{
                return $this->response(101, '编辑失败');
            }

        }


    }


    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * type放入回收站
     */

    public function clearType(Request $request){
        $param = $request->param();
        //回收站id
        $clear_type_id = Type::where([['title','=','回收站'],['user_id','=',$this->user_id]])->value('id');

        $info = Type::where([['id','=', $param['id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            return $this->response(102, '文件夹不存在');
        }else{
            $info->parent_id = $clear_type_id;
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '删除成功');
            }else{
                return $this->response(101, '删除失败');
            }

        }


    }




    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * content放入回收站
     */

    public function clearContent(Request $request){
        $param = $request->param();
        //回收站id
        $clear_type_id = Type::where([['title','=','回收站'],['user_id','=',$this->user_id]])->value('id');

        $info = Content::where([['id','=', $param['id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            return $this->response(102, '文件夹不存在');
        }else{
            $info->type_id = $clear_type_id;
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '删除成功');
            }else{
                return $this->response(101, '删除失败');
            }

        }


    }


    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * type拖拽移动
     */

    public function moveType(Request $request){
        $param = $request->param();

        $info = Type::where([['id','=', $param['id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            return $this->response(102, '文件夹不存在');
        }else{

            $info->parent_id = $param['parent_id'];
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '移动成功');
            }else{
                return $this->response(101, '移动失败');
            }

        }


    }



    /**
     * Created by ngt<ninggutoeng.com>.
     * Date: 2021/1/2
     * Time: 8:14 下午
     * type置顶
     */

    public function topType(Request $request){
        $param = $request->param();

        $info = Type::where([['id','=', $param['id']],['user_id','=',$this->user_id]])->findOrEmpty();
        if ($info->isEmpty()){
            return $this->response(102, '文件夹不存在');
        }else{

            $info->top = 1;
            $result = $info->save($param);
            if ($result){
                return $this->response(200, '置顶成功');
            }else{
                return $this->response(101, '置顶失败');
            }

        }


    }
}
