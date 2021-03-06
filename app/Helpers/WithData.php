<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;
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
 *public function tranning()		  []
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
        	$this->error = handleResponseJson(203, '请选择一个证书类型');
        	return false;
        }

        uppro('img1',$fields);
        uppro('img2',$fields);
        uppro('img3',$fields);
        uppro('img4',$fields);
        uppro('img5',$fields);
        uppro('img6',$fields);

        $this->logInsert = "添加证书: ".$fields['title'];
        $this->logUpdate = '更新证书: '.$fields['title'];
        return $fields;
    }

    public function education()
    {
        if(in_array($GLOBALS['tty'], [24,25,29,30])){
            $rules = [
                'title' => 'required',
                'ftitle' => 'required',
                'from' => 'required',
                'destination' => 'required',
                'introduce' => 'required',
                'content' => 'required',
                'starttime' => 'required|date',
                'endtime' => 'required|date',
                'bstarttime' => 'required|date',
                'bendtime' => 'required|date',
                'content' => 'required',
                'content2' => 'required',
            ];
            $message = [
                'title.required' => '标题 项必填',
                'ftitle.required' => '推荐人群 项必填',
                'from.required' => '出发地 项必填',
                'destination.required' => '目的地 项必填',
                'introduce.required' => '席位情况 项必填',
                'starttime.required' => '游学开始时间 项必填',
                'starttime.date' => '游学开始时间 格式不正确',
                'endtime.required' => '游学结束时间 项必需',
                'endtime.date' => '游学结束时间 格式不正确',
                'bstarttime.required' => '报名开始时间 项必需',
                'bstarttime.date' => '报名开始时间 格式不正确',
                'bendtime.required' => '报名结束时间 项必需',
                'bendtime.date' => '报名结束时间 格式不正确',
                'content.required' => '路线详情 项必需',
                'content2.required' => '详情介绍 项必填',
            ];
        }elseif($GLOBALS['tty']<>27){
            $rules = [
                'title' => 'required',
                'content' => 'required',
            ];
            $message = [
                'title.required' => '标题 项必填',
                'content.required' => '详情内容 项必填',
            ];
        } else {
            $rules = [
                'title' => 'required',
            ];
            $message = [
                'title.required' => '标题 项必填',
            ];
        }


        $validator = Validator::make($this->data, $rules, $message);

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            $this->error = noticeResponseJson(412, '执行失败', $errors);
            return false;
        }
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



         uppro('img1',$fields);
         uppro('img2',$fields);
         uppro('img3',$fields);
         uppro('img4',$fields);
         uppro('img5',$fields);
         uppro('img6',$fields);
//         uppro('file',$fields,'file');
        $this->logInsert = '添加教育信息('.$fields['pid'].','.$fields['ty'].','.$fields['tty'].'): '.$fields['title'];
        $this->logUpdate = '更新教育信息('.$fields['pid'].','.$fields['ty'].','.$fields['tty'].'): '.$fields['title'];
        return $fields;
    }

    public function nature()
    {
        $fields=array(
            'pid' 		  => 		(int)$GLOBALS['pid'],
            'typeid' 		  => 	I('post.typeid',0,'intval'),
            'catname'     => 		I('post.catname','','trim'),
            'catname2'    => 		I('post.catname2','','trim'),
            'linkurl'     =>	 	I('post.linkurl','','trim'),
            'hits'  =>	 	0,
            'isgood'    =>	 	I('post.showtype',1,'intval'),
            'disorder'    =>	 	I('post.disorder',0,'intval'),
            'isstate'      =>	 	1,
        );

        uppro('img1',$fields);
        uppro('img2',$fields);
        $this->logInsert = '添加类别分类'.$fields['catname'];
        $this->logUpdate = '编辑类别分类'.$fields['catname'];

        return $fields;
    }

    public function training()
    {
        $rules = [
            'title' => 'required',
            'name' => 'required',
            'period' => 'required',
            'price' => 'required',
            'introduce' => 'required',
            'content' => 'required',
            'content2' => 'required',
            'content3' => 'required',
            'content4' => 'required',
        ];
        $message = [
            'title.required' => '课程名称 项必填',
            'name.required' => '讲师姓名 项必填',
            'period.required' => '课时 项必填',
            'price.required' => '价格 项必填',
            'introduce.required' => '开播时间 项必填',
            'content.required' => '课程介绍 项必填',
            'content2.required' => '课程大纲 项必填',
            'content3.required' => '讲师介绍 格式不正确',
            'content4.required' => '游学结束时间 项必填',
        ];
        $validator = Validator::make($this->data, $rules, $message);

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            $this->error = noticeResponseJson(412, '执行失败', $errors);
            return false;
        }

        $fields = array(
            'pid'               =>      (int)$GLOBALS['pid'],
            'ty'                =>      (int)$GLOBALS['ty'],
            'tty'               =>      0,
            'industryid'        =>      $this->I('industryid', 0, 'intval'),
            'neixunid'          =>      $this->I('neixunid', 0, 'intval'),
            'trainingid'        =>      $this->I('trainingid', 0, 'intval'),
            'qualificationid'   =>      $this->I('qualificationid', 0, 'intval'),
            'publicid'          =>      $this->I('publicid', 0, 'intval'),
            'title'             =>      $this->I('title','','htmlspecialchars'),
            'name'              =>      $this->I('name','','htmlspecialchars'),
            'ftitle'            =>      $this->I('ftitle','','htmlspecialchars'),
            'content'           =>      $this->I('content',''),
            'content2'          =>      $this->I('content2',''),
            'content3'          =>      $this->I('content3',''),
            'content4'          =>      $this->I('content4',''),
            'introduce'         =>      $this->I('introduce','','htmlspecialchars'),
            'price'             =>      $this->I('price', 0.00,'floatval'),
            'period'            =>      $this->I('period','','htmlspecialchars'),
            'sendtime'          =>      time(),
        );
        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        uppro('img1',$fields);
        uppro('img2',$fields);
        uppro('img3',$fields);
        uppro('img4',$fields);
        uppro('img5',$fields);
        uppro('img6',$fields);
        // uppro('file',$fields);
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = '添加培训信息('.$fields['pid'].','.$fields['ty'].','.'): '.$fields['title'];
        $this->logUpdate = '更新培训信息('.$fields['pid'].','.$fields['ty'].','.'): '.$fields['title'];
        return $fields;
    }

    public function job()
    {
//        dd(request()->all());
        $rules = [
            'title' => 'required',
            'address' => 'required',
            'work_nature' => 'required',
            'salary' => 'required',
            'relative' => 'required',
            'recruit_num' => 'required',
            'endtime' => 'required|date',
            'education' => 'required',
            'experience' => 'required',
            'content2' => 'required',
            'content' => 'required',
        ];
        $message = [
            'title.required' => '职位名称 项必填',
            'address.required' => '职位发布地址 项必填',
            'work_nature.required' => '职位性质 项必选',
            'salary.required' => '职位月薪 项必选',
            'relative.required' => '亮点标签标签 项必选',
            'recruit_num.required' => '招收人数 项必填',
            'endtime.required' => '招聘结束时间 项必需',
            'endtime.date' => '招聘结束时间 格式不正确',
            'education.required' => '学历要求 项必填',
            'experience.required' => '经验要求 项必填',
            'content2.required' => '其他要求 项必填',
            'content.required' => '职位描述 项必填',
        ];

        $validator = Validator::make($this->data, $rules, $message);

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            $this->error = noticeResponseJson(412, '执行失败', $errors);
            return false;
        }
//
//        $relative = $this->I('relative', [],'');
//        $relative = $relative && implode(',', $relative);
        $fields = array(
            'pid'				=>		(int)$GLOBALS['pid'],
            'ty'				=>		(int)$GLOBALS['ty'],
            'tty'				=>		0,
            'title'				=>		$this->I('title','','htmlspecialchars'),
            'address'           =>      $this->I('address','','htmlspecialchars'),
            'industryid'        =>      $this->I('industryid', 0, 'intval'),
            'positionid'        =>      $this->I('positionid', 0, 'intval'),
            'work_nature'       =>      $this->I('work_nature', 0, 'intval'),
            'salary'            =>      $this->I('salary', 0, 'intval'),
            'relative'          =>       $this->I('relative', '',''),
            'recruit_num'       =>      $this->I('recruit_num', ''),
            'endtime'           =>      $this->I('endtime', time()+3600*24*7, 'strtotime'),
            'education'         =>      $this->I('education', 0, 'intval'),
            'experience'        =>      $this->I('experience', 0, 'intval'),
            'content2'          =>      $this->I('content2',''),
            'content'           =>      $this->I('content',''),
            'issued'            =>      1,
            'sendtime'      	=>		time(),
        );


        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        // uppro('img1',$fields);
        // uppro('img2',$fields);
        // uppro('img3',$fields);
        // uppro('img4',$fields);
        // uppro('img5',$fields);
        // uppro('img6',$fields);
        // uppro('file',$fields);
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = '添加招聘信息('.$fields['pid'].','.$fields['ty'].'): '.$fields['title'];
        $this->logUpdate = '更新招聘信息('.$fields['pid'].','.$fields['ty'].'): '.$fields['title'];
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
		    $val = $this->data[$get];
		   /* if( $filter) {
                $val = call_user_func($filter, $val);
            }*/
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
        if ($this->error) {
            return false;
        }
		if ( $id ) {// 执行更新
			// $this->fields['id'] = $id;
			$where = ['id' => $id, 'user_id' => $user_id];

            $this->fields['updated_at'] = date('Y-m-d H:i:s');


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
            $this->fields['created_at'] = date('Y-m-d H:i:s');

			if($insert = DB::table($table)->insert($this->fields) ) {

				$logInsert && \App\Log::create(['details' => $logInsert, 'user_id' => $user_id]);

				return [200, '添加数据成功'];
			}else{
				return [412, '添加数据失败!'];
			}
		}
	}

}
