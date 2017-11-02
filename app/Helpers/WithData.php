<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
/**
 *public function submit()     统一提交
 *#以下方法名对应表名称
 *public function news()      []
 *public function config()    []
 *public function content()   [] 映射->news表
 *public function news_cats() [] news_cats表(超级管理员用的)
 *public function pic()		  []
 *public function usr()		  []
 *public function education()		  []
 *public function certificate()		  []
 *public function nature()		  []
 */
class WithData
{

	protected $fields = [];
	protected $table = '';
	protected $isUpdate = 0;
	protected $isInsert = 0;
	protected $logInsert = '';// 插入数据时的日志
	protected $logUpdate = '';// 更新时的日志

	private $data = [];
	public $error = [];

	// 表映射
	private static $map = [
		'content' => 'news',
	];

	public function __construct($table, $id, $data)
	{
		if ( $id ) {
			$this->isUpdate = $id;
		} else {
			$this->isInsert = 1;
		}

		$this->data = $data;

		$this->table  = $table;
		$this->fields = $this->$table();
		isset(self::$map[$table]) && $this->table = self::$map[$table];

	}

    public function certificate()
    {
        $certificate_lid = $this->I('certificate_lid', 0, 'intval');

        $fields = array(
            'pid'               =>      (int)$GLOBALS['pid'],
            'ty'                =>      (int)$GLOBALS['ty'] ? : 11,
            'tty'               =>      (int)$GLOBALS['tty'] ? : 54,
            'certificate_lid'   =>      $this->I('certificate_lid', 0, 'intval'),
            'title'             =>      $this->I('title'),
            'content'           =>      $this->I('content'),
            'sendtime'          =>      time(),

        );
        if ($fields['tty']==54 && empty($certificate_lid)) {
        	$this->error = [203, '请选择一个证书类型'];
        	return false;
        }

        $this->logInsert = "添加证书: ".$fields['title'];
        $this->logUpdate = '更新证书: '.$fields['title'];
        return $fields;
    }

    public function education()
    {
        // $file = \Input::file('img1');

        $fields = array(
            'pid'               =>      (int)$GLOBALS['pid'],
            'ty'                =>      (int)$GLOBALS['ty'] ? : 11,
            'tty'               =>      (int)$GLOBALS['tty'] ? : 54,
            'title'             =>      $this->I('title'),
            'ftitle'            =>      $this->I('ftitle'),
            'from'              =>      $this->I('from'),
            'destination'       =>      $this->I('destination'),
            'introduce'         =>      $this->I('introduce'),
            'content'           =>      $this->I('content'),
            'content2'          =>      $this->I('content2'),
            'sendtime'          =>      time(),

            'starttime'         =>      $this->I('starttime', time(), 'strtotime'),
            'endtime'           =>      $this->I('endtime', time(), 'strtotime'),
            'bstarttime'        =>      $this->I('bstarttime', time(), 'strtotime'),
            'bendtime'          =>      $this->I('bendtime', time(), 'strtotime'),

        );



         uppro('img1',$fields,'ajax');
        // uppro('img2',$fields,'ajax');
        // uppro('img3',$fields,'ajax');
        // uppro('img4',$fields,'ajax');
        // uppro('img5',$fields,'ajax');
        // uppro('img6',$fields,'ajax');
        // uppro('file',$fields,'file');
        $this->logInsert = '添加教育信息('.$fields['pid'].','.$fields['ty'].','.$fields['tty'].'): '.$fields['title'];
        $this->logUpdate = '更新教育信息('.$fields['pid'].','.$fields['ty'].','.$fields['tty'].'): '.$fields['title'];
        return $fields;
    }

    public function nature()
    {
        $fields=array(
            'pid' 		  => 		I('post.pid',0,'intval'),
            'typeid' 		  => 		I('post.typeid',0,'intval'),
            'catname'     => 		I('post.catname','','trim'),
            'catname2'    => 		I('post.catname2','','trim'),
            'linkurl'     =>	 	I('post.linkurl','','trim'),
            'hits'  =>	 	0,
            'isgood'    =>	 	I('post.showtype',1,'intval'),
            'disorder'    =>	 	I('post.disorder',0,'intval'),
            'isstate'      =>	 	1,
        );

        uppro('img1',$fields,'ajax');
        uppro('img2',$fields,'ajax');

        $this->logInsert = '添加类别分类'.$fields['catname'];
        $this->logUpdate = '编辑类别分类'.$fields['catname'];

        return $fields;
    }
	public function pic()
	{
		$fields = array(
			'ti'			=>	I('post.ti',0,'intval'),
			'disorder'		=>	I('post.disorder',0,'intval'),
			'title'		=>	I('post.title',''),
			'content'		=>	I('post.content',''),
			'linkurl'		=>	I('post.linkurl','','trim,htmlspecialchars'),
			'sendtime'		=>	I('post.sendtime',0,'strtotime'),
            'isstate'      =>	 	1,
		);
		uppro('img1',$fields,'ajax');
		uppro('img2',$fields,'ajax');
		$this->logInsert = '添加图片->'.$fields['title'];
		$this->logUpdate = '编辑图片->'.$fields['title'];
		return $fields;

	}

	//用户
	public function usr()
	{
		$fields = array(
			'state' => I('state', 0, 'intval'),
			'expired' => I('expired', time(), 'strtotime'),
		);
		$this->logInsert = '';
		$this->logUpdate = '用户修改';
		return $fields;

	}

	private function I($get, $default='', $filter='htmlspecialchars')
	{
		if (isset($this->data[$get])) {
			$val = call_user_func($filter, $this->data[$get]);
			return $val ? : $default;
		}
		return $default;
	}

	// 提交数据
	public function submit($user_id)
	{
		$id        = $this->isUpdate;
		$table     = $this->table;
		$logUpdate = $this->logUpdate;
		$logInsert = $this->logInsert;
		if (!$this->fields) {
			return $this->error;
		}
		if ( $id ) {// 执行更新
			// $this->fields['id'] = $id;
			$where = ['id' => $id, 'user_id' => $user_id];

			if($update = DB::table($table)
				->where('id', $id)
				->where('user_id', $user_id)
				->update($this->fields) ) {

				$logUpdate && \App\Log::create(['details' => $logUpdate, 'user_id' => $user_id]);

				return [200, '更新数据成功'];
			}else{
            }
            return [200, '更新数据成功!'];
        }else{// 执行插入

			$this->fields['user_id'] = $user_id;

			if($insert = DB::table($table)->insert($this->fields) ) {

				$logInsert && \App\Log::create(['details' => $logInsert, 'user_id' => $user_id]);

				return [200, '添加数据成功'];
			}else{
				return [412, '添加数据失败!'];
			}
		}
	}

}