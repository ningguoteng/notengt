<?php
// 应用公共文件
use app\model\User;
use app\model\Type;

/**
 * Created by ngt<ningguoteng.com>.
 * Date: 2020/12/30
 * Time: 2:16 下午
 * 生成密码
 * @param str $password
 */
function build_password($password,$key='notengt'){
    return md5($password . md5($key));
}

/**
 * Created by ngt<ningguoteng.com>.
 * Date: 2020/12/31
 * Time: 5:37 下午
 * @param int $parent_id
 * @param array $data
 * 递归查询type目录
 */

function getSonTypes($user_id, $parent_id, $data=[]){
    $userInfo = User::find($user_id);
    $list = $userInfo->types()->field('id,parent_id,parent_id as pId,title as name')->where([['parent_id','=',$parent_id],['status','=',1]])->select();

    if(!$list->isEmpty()){
        $list = $list->toArray();
        $data = array_merge($data,$list);
        foreach ($list as $k=>$v){
            $data = getSonTypes($user_id, $v['id'],$data);
        }
        return  $data;
    }else{
        return $data;
    }


}

/**
 * @param str $code
 * @param str $message
 * @param array $data
 * 公共返回方法
 */
function response($code, $message='', $data=[])
{
    $arr = ['code'=>$code,'message'=>$message];
    if ($code == 200){
        $arr['data'] = $data;
    }
    echo json_encode($arr);

}

/**
 * Created by ngt<ninggutoeng.com>.
 * Date: 2021/1/2
 * Time: 8:38 下午
 * @param int $user_id 用户id
 *
 */

function getTopTypeId($user_id){

   return Type::where([['user_id','=',$user_id],['status','=',1],['parent_id','=',0]])->value('id');
}