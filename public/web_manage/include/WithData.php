<?php
/**
 *public function submit()     统一提交
 *#以下方法名对应表名称
 *public function news()      []
 *public function config()    []
 *public function content()   [] 映射->news表
 *public function news_cats() [] news_cats表(超级管理员用的)
 *public function pic()		  []
 *public function usr()		  []
 */
class WithData
{

	protected $fields = [];
	protected $table = '';
	protected $isUpdate = 0;
	protected $isInsert = 0;
	protected $logInsert = '';// 插入数据时的日志
	protected $logUpdate = '';// 更新时的日志

	// 表映射
	private static $map = [
		'content' => 'news',
	];

	public function __construct($table, $id)
	{
		if ( $id ) {
			$this->isUpdate = $id;
		} else {
			$this->isInsert = 1;
		}

		$this->table  = $table;
		$this->fields = $this->$table();
		isset(self::$map[$table]) && $this->table = self::$map[$table];

	}


	// 提交数据
	public function submit()
	{
		$id        = $this->isUpdate;
		$table     = $this->table;
		$logUpdate = $this->logUpdate;
		$logInsert = $this->logInsert;
		if ( $id ) {// 执行更新

			$this->fields['id'] = $id;

			if($update = M($table)->update($this->fields) ) {

				$logUpdate && AddLog($logUpdate, $_SESSION['Admin_UserName']);

				return [1, '更新数据成功'];
			}else{
				return [0, '更新数据失败!'];
			}
		}else{// 执行插入

			$this->fields['isstate'] = 1;

			if($insert = M($table)->insert($this->fields) ) {

				$logInsert && AddLog($logInsert, $_SESSION['Admin_UserName']);

				return [6, '添加数据成功'];
			}else{
				return [5, '添加数据失败!'];
			}
		}
	}


	public function news()
	{
		$istop = I('post.istop',0,'intval');

		$relative = isset($_POST['relative']) && is_array($_POST['relative'])?implode(',',$_POST['relative']):'';
		$fields = array(
			'pid'				=>		I('pid', 0, 'intval'),
			'ty'				=>		I('ty' , 0, 'intval'),
			'tty'				=>		I('tty', 0, 'intval'),
			'academyid'				=>		I('academyid', 0, 'intval'),
			'infotypeid'				=>		I('infotypeid', 0, 'intval'),
			'title'				=>		I('post.title','','trim,htmlspecialchars'),
			'ftitle'			=>		I('post.ftitle','','trim,htmlspecialchars'),
			'content'			=>		I('post.content',''),
			'content2'       	=>		I('post.content2',''),
			'content3'       	=>		I('post.content3',''),
			'content4'       	=>		I('post.content4',''),
			'content5'       	=>		I('post.content5',''),
			'name'				=>		I('post.name','','trim'),
			'source'			=>		I('post.source','','trim,htmlspecialchars'),
			'destination'			=>		I('post.destination','','trim,htmlspecialchars'),
			'relative'			=>		$relative,
			'introduce'			=>		I('post.introduce','','trim,htmlspecialchars'),
			'price'				=>		I('post.price','','trim,htmlspecialchars'),
			'linkurl'			=>		I('post.linkurl','','trim,htmlspecialchars'),
			'link1'				=>		I('post.link1','','trim,htmlspecialchars'),
			'link2'				=>		I('post.link2','','trim,htmlspecialchars'),
			#资讯
			'begin'      	=>		I('post.begin','','trim,htmlspecialchars'),
			//SEO
			'seotitle'		    =>		I('post.seotitle','','trim'),
			'keywords'		    =>		I('post.keywords','','trim'),
			'description'		=>		I('post.description','','trim'),

			'disorder'      	=>		I('post.disorder',0,'intval'),
			'hits'      		=>		I('post.hits',1,'intval'),
			'istop'      	 	=>		I('post.istop',0,'intval'),
			'isgood'      	 	=>		I('post.isgood',0,'intval'),
			'sendtime'      	=>		I('post.sendtime',0,'strtotime'),

		);
		/*if ($fields['ty'] == 9 && empty($fields['istop'])) {
			ajaxReturn(-1,'请选择案例分类');
		}*/
		uppro('img1',$fields,'ajax');
		uppro('img2',$fields,'ajax');
		uppro('img3',$fields,'ajax');
		uppro('img4',$fields,'ajax');
		uppro('img5',$fields,'ajax');
		uppro('img6',$fields,'ajax');
		uppro('file',$fields,'file');
		// uppro('img5',$fields,'water',$water_path);
		$this->logInsert = "添加信息: ".$fields['title'];
		$this->logUpdate = '更新信息: '.$fields['title'];
		return $fields;
	}

    public function education()
    {
        $istop = I('post.istop',0,'intval');

        $relative = isset($_POST['relative']) && is_array($_POST['relative'])?implode(',',$_POST['relative']):'';
        $fields = array(
            'pid'                =>    		I('pid', 0, 'intval'),
            'ty'                 =>    		I('ty' , 0, 'intval'),
            'tty'                =>    		I('tty', 0, 'intval'),
            'title'              =>    		I('post.title','','trim,htmlspecialchars'),
            'ftitle'             =>    		I('post.ftitle','','trim,htmlspecialchars'),
            'content'            =>    		I('post.content',''),
            'content2'           =>    		I('post.content2',''),
            'content3'           =>    		I('post.content3',''),
            'content4'           =>    		I('post.content4',''),
            'content5'           =>    		I('post.content5',''),
            'name'                =>    		I('post.name','','trim'),
            'source'              =>    		I('post.source','','trim,htmlspecialchars'),
            'destination'        =>    		I('post.destination','','trim,htmlspecialchars'),
            'from'        =>    		I('post.from','','trim,htmlspecialchars'),
            'relative'            =>    		$relative,
            'introduce'           =>    		I('post.introduce','','trim,htmlspecialchars'),
            'price'                =>    		I('post.price','','trim,htmlspecialchars'),
            'linkurl'              =>    		I('post.linkurl','','trim,htmlspecialchars'),
            'link1'                =>    		I('post.link1','','trim,htmlspecialchars'),
            'link2'                =>    		I('post.link2','','trim,htmlspecialchars'),
                        #资讯
            'begin'      	      =>    		I('post.begin','','trim,htmlspecialchars'),
                        //SEO
            'seotitle'		      =>    		I('post.seotitle','','trim'),
            'keywords'		      =>    		I('post.keywords','','trim'),
            'description'		  =>    		I('post.description','','trim'),

            'disorder'      	=>		I('post.disorder',0,'intval'),
            'hits'      		=>		I('post.hits',1,'intval'),
            'istop'      	 	=>		I('post.istop',0,'intval'),
            'isgood'      	 	=>		I('post.isgood',0,'intval'),
            'sendtime'      	=>		I('post.sendtime',0,'strtotime'),
            'starttime'      	=>		I('post.starttime',0,'strtotime'),
            'endtime'      	=>		I('post.endtime',0,'strtotime'),
            'bstarttime'      =>		I('post.bstarttime',0,'strtotime'),
            'bendtime'      	=>		I('post.bendtime',0,'strtotime'),

        );
        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        uppro('img1',$fields,'ajax');
        uppro('img2',$fields,'ajax');
        uppro('img3',$fields,'ajax');
        uppro('img4',$fields,'ajax');
        uppro('img5',$fields,'ajax');
        uppro('img6',$fields,'ajax');
        uppro('file',$fields,'file');
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = "添加信息: ".$fields['title'];
        $this->logUpdate = '更新信息: '.$fields['title'];
        return $fields;
    }
    public function certificate()
    {
        $istop = I('post.istop',0,'intval');

        $relative = isset($_POST['relative']) && is_array($_POST['relative'])?implode(',',$_POST['relative']):'';
        $fields = array(
            'pid'				=>		I('pid', 0, 'intval'),
            'ty'				=>		I('ty' , 0, 'intval'),
            'tty'				=>		I('tty', 0, 'intval'),
            'certificate_lid'				=>		I('post.certificate_lid', 0, 'intval'),
            'title'				=>		I('post.title','','trim,htmlspecialchars'),
            'ftitle'			=>		I('post.ftitle','','trim,htmlspecialchars'),
            'content'			=>		I('post.content',''),
            'content2'       	=>		I('post.content2',''),
            'content3'       	=>		I('post.content3',''),
            'content4'       	=>		I('post.content4',''),
            'content5'       	=>		I('post.content5',''),
            'name'				=>		I('post.name','','trim'),
            'source'			=>		I('post.source','','trim,htmlspecialchars'),
            'destination'			=>		I('post.destination','','trim,htmlspecialchars'),
            'relative'			=>		$relative,
            'introduce'			=>		I('post.introduce','','trim,htmlspecialchars'),
            'price'				=>		I('post.price','','trim,htmlspecialchars'),
            'linkurl'			=>		I('post.linkurl','','trim,htmlspecialchars'),
            'link1'				=>		I('post.link1','','trim,htmlspecialchars'),
            'link2'				=>		I('post.link2','','trim,htmlspecialchars'),
            #资讯
            'begin'      	=>		I('post.begin','','trim,htmlspecialchars'),
            //SEO
            'seotitle'		    =>		I('post.seotitle','','trim'),
            'keywords'		    =>		I('post.keywords','','trim'),
            'description'		=>		I('post.description','','trim'),

            'disorder'      	=>		I('post.disorder',0,'intval'),
            'hits'      		=>		I('post.hits',1,'intval'),
            'istop'      	 	=>		I('post.istop',0,'intval'),
            'isgood'      	 	=>		I('post.isgood',0,'intval'),
            'sendtime'      	=>		I('post.sendtime',0,'strtotime'),
            'starttime'      	=>		I('post.starttime',0,'strtotime'),
            'endtime'      	=>		I('post.endtime',0,'strtotime'),
            'bstarttime'      	=>		I('post.bstarttime',0,'strtotime'),
            'bendtime'      	=>		I('post.bendtime',0,'strtotime'),

        );
        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        uppro('img1',$fields,'ajax');
        uppro('img2',$fields,'ajax');
        uppro('img3',$fields,'ajax');
        uppro('img4',$fields,'ajax');
        uppro('img5',$fields,'ajax');
        uppro('img6',$fields,'ajax');
        uppro('file',$fields,'file');
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = "添加信息: ".$fields['title'];
        $this->logUpdate = '更新信息: '.$fields['title'];
        return $fields;
    }

    public function training()
    {
        $istop = I('post.istop',0,'intval');
        $fields = array(
            'pid'				=>		I('pid', 0, 'intval'),
            'ty'				=>		I('ty' , 0, 'intval'),
            'tty'				=>		I('tty', 0, 'intval'),
            'industryid'				=>		I('post.industryid', 0, 'intval'),
            'neixunid'				=>		I('post.neixunid', 0, 'intval'),
            'trainingid'				=>		I('post.trainingid', 0, 'intval'),
            'qualificationid'				=>		I('post.qualificationid', 0, 'intval'),
            'publicid'				=>		I('post.publicid', 0, 'intval'),
            'title'				=>		I('post.title','','trim,htmlspecialchars'),
            'ftitle'			=>		I('post.ftitle','','trim,htmlspecialchars'),
            'content'			=>		I('post.content',''),
            'content2'       	=>		I('post.content2',''),
            'content3'       	=>		I('post.content3',''),
            'content4'       	=>		I('post.content4',''),
            'content5'       	=>		I('post.content5',''),
            'name'				=>		I('post.name','','trim'),
            'source'			=>		I('post.source','','trim,htmlspecialchars'),
            'destination'			=>		I('post.destination','','trim,htmlspecialchars'),
            'introduce'			=>		I('post.introduce','','trim,htmlspecialchars'),
            'price'				=>		I('post.price','','trim,htmlspecialchars'),
            'linkurl'			=>		I('post.linkurl','','trim,htmlspecialchars'),
            'period'				=>		I('post.period','','trim,htmlspecialchars'),
            'link1'				=>		I('post.link1','','trim,htmlspecialchars'),
            'link2'				=>		I('post.link2','','trim,htmlspecialchars'),
            #资讯
            //SEO
            'seotitle'		    =>		I('post.seotitle','','trim'),
            'keywords'		    =>		I('post.keywords','','trim'),
            'description'		=>		I('post.description','','trim'),

            'disorder'      	=>		I('post.disorder',0,'intval'),
            'hits'      		=>		I('post.hits',1,'intval'),
            'istop'      	 	=>		I('post.istop',0,'intval'),
            'isgood'      	 	=>		I('post.isgood',0,'intval'),
            'sendtime'      	=>		I('post.sendtime',0,'strtotime'),
            'starttime'      	=>		I('post.starttime',0,'strtotime'),
            'endtime'      	=>		I('post.endtime',0,'strtotime'),
            'bstarttime'      	=>		I('post.bstarttime',0,'strtotime'),
            'bendtime'      	=>		I('post.bendtime',0,'strtotime'),

        );
        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        uppro('img1',$fields,'ajax');
        uppro('img2',$fields,'ajax');
        uppro('img3',$fields,'ajax');
        uppro('file',$fields,'file');
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = "添加信息: ".$fields['title'];
        $this->logUpdate = '更新信息: '.$fields['title'];
        return $fields;
    }

    public function job()
    {
        $istop = I('post.istop',0,'intval');
        $relative = isset($_POST['relative']) && is_array($_POST['relative'])?implode(',',$_POST['relative']):'';
        $fields = array(
            'pid'				=>		I('pid', 0, 'intval'),
            'ty'				=>		I('ty' , 0, 'intval'),
            'tty'				=>		I('tty', 0, 'intval'),
            'nature'				=>		I('post.nature', 0, 'intval'),
            'work_nature'				=>		I('post.work_nature', 0, 'intval'),
            'industryid'				=>		I('post.industryid', 0, 'intval'),
            'positionid'				=>		I('post.positionid', 0, 'intval'),
            'salary'				=>		I('post.salary', 0, 'intval'),
            'education'				=>		I('post.education', 0, 'intval'),
            'experience'				=>		I('post.experience', 0, 'intval'),
            'title'				=>		I('post.title','','trim,htmlspecialchars'),
            'ftitle'			=>		I('post.ftitle','','trim,htmlspecialchars'),
            'recruit_num'			=>		I('post.recruit_num','','trim,htmlspecialchars'),
            'content'			=>		I('post.content',''),
            'content2'       	=>		I('post.content2',''),
            'content3'       	=>		I('post.content3',''),
            'content4'       	=>		I('post.content4',''),
            'content5'       	=>		I('post.content5',''),
            'name'				=>		I('post.name','','trim'),
            'source'			=>		I('post.source','','trim,htmlspecialchars'),
            'address'			=>		I('post.address','','trim,htmlspecialchars'),
            'destination'			=>		I('post.destination','','trim,htmlspecialchars'),
            'introduce'			=>		I('post.introduce','','trim,htmlspecialchars'),
            'price'				=>		I('post.price','','trim,htmlspecialchars'),
            'linkurl'			=>		I('post.linkurl','','trim,htmlspecialchars'),
            'period'				=>		I('post.period','','trim,htmlspecialchars'),
            'link1'				=>		I('post.link1','','trim,htmlspecialchars'),
            'link2'				=>		I('post.link2','','trim,htmlspecialchars'),
            #资讯
            //SEO
            'seotitle'		    =>		I('post.seotitle','','trim'),
            'keywords'		    =>		I('post.keywords','','trim'),
            'description'		=>		I('post.description','','trim'),
            'relative'			=>		$relative,
            'disorder'      	=>		I('post.disorder',0,'intval'),
            'hits'      		=>		I('post.hits',1,'intval'),
            'istop'      	 	=>		I('post.istop',0,'intval'),
            'isgood'      	 	=>		I('post.isgood',0,'intval'),
            'sendtime'      	=>		I('post.sendtime',0,'strtotime'),

        );
        /*if ($fields['ty'] == 9 && empty($fields['istop'])) {
            ajaxReturn(-1,'请选择案例分类');
        }*/
        uppro('img1',$fields,'ajax');
        uppro('img2',$fields,'ajax');
        uppro('img3',$fields,'ajax');
        uppro('img4',$fields,'ajax');
        uppro('img5',$fields,'ajax');
        uppro('img6',$fields,'ajax');
        uppro('file',$fields,'file');
        // uppro('img5',$fields,'water',$water_path);
        $this->logInsert = "添加信息: ".$fields['title'];
        $this->logUpdate = '更新信息: '.$fields['title'];
        return $fields;
    }
	/**
	 * [config config.php]
	 * @return [type] [提交过来的]
	 */
	public function config()
	{
		$fields=[];

		$fields['sitename']		=	I('post.sitename', '', 'trim');
		if($_SESSION['is_hidden']==true){
			$fields['isstate']	=	I('post.isstate', 0, 'intval');
			$fields['showinfo']	=	I('post.showinfo', '', 'trim,htmlspecialchars');
		}
		$fields['filetype']		=	I('post.filetype', '', 'trim,htmlspecialchars');
		$fields['filesize']		=	I('post.filesize', '', 'trim,htmlspecialchars');
		$fields['pictype']		=	I('post.pictype', '', 'trim,htmlspecialchars');
		$fields['picsize']		=	I('post.picsize', '', 'trim,htmlspecialchars');
		$fields['hotsearch']	=	I('post.hotsearch', '', 'trim,htmlspecialchars');
		$fields['seotitle']		=	I('post.seotitle', '', 'trim,htmlspecialchars');
		$fields['keywords']		=	I('post.keywords', '', 'trim,htmlspecialchars');
		$fields['description']  =	I('post.description', '', 'trim,htmlspecialchars');
		$fields['isrewrite']=0;//伪静态

		$fields['indexabout']	=	I('post.indexabout', '', 'trim,htmlspecialchars');
		$fields['indexcontact'] =	I('post.indexcontact', '', 'trim,htmlspecialchars');

		$fields['link1']		=	I('post.link1', '', 'trim,htmlspecialchars');#普通团车
		$fields['link2']		=	I('post.link2', '', 'trim,htmlspecialchars');#团子秒车
		$fields['link3']		=	I('post.link3', '', 'trim,htmlspecialchars');#团子秒车
		$fields['link4']		=	I('post.link4', '', 'trim,htmlspecialchars');#团子秒车
		$fields['link5']		=	I('post.link5', '', 'trim,htmlspecialchars');#团子秒车
		$fields['link6']		=	I('post.link6', '', 'trim,htmlspecialchars');#团子秒车
		#微信
		$fields['oauth']		=	I('post.oauth', '', 'trim,htmlspecialchars');#网页验证
		$fields['appid']		=	I('post.appid', '', 'trim,htmlspecialchars');#公众号id
		$fields['appsecret']	=	I('post.appsecret', '', 'trim,htmlspecialchars');#公众token

		$fields['email']		=	I('post.email', '', 'trim,htmlspecialchars');#邮箱
		$fields['tel']			=	I('post.tel', '', 'trim,htmlspecialchars');
		$fields['fax']			=	I('post.fax', '', 'trim');
		$fields['phone']		=	I('post.phone', '', 'trim,htmlspecialchars');
		$fields['address']		=	I('post.address', '', 'trim,htmlspecialchars');#地址
		$fields['siteurl']		=	I('post.siteurl', '', 'trim,htmlspecialchars');#pc端地址
		$fields['siteurl_wap']	=	I('post.siteurl_wap', '', 'trim,htmlspecialchars');#手机端地址
		$fields['webqq']		=	I('post.webqq', '', 'trim,htmlspecialchars');
		$fields['icpcode']		=	I('post.icpcode', '', 'trim,htmlspecialchars');//备案号
		//textarea
		$fields['header']     	=   I('post.header', '', '');//全局代码一般
		$fields['copyright']  	=   I('post.copyright', '', '');//版权信息 不做处理
		uppro('logo1', $fields, 'ajax');
		uppro('logo2', $fields, 'ajax');
		uppro('img1', $fields, 'ajax');
		uppro('file',$fields,'file');

		// $this->logInsert = '编辑系统信息';
		$this->logUpdate = '编辑系统信息';

		return $fields;
	}

	// content.php
	public function content()
	{
		$ty = I('post.ty',  0, 'intval');
		$fields = array(
			'pid'			=>	I('post.pid', 0, 'intval'),
			'ty'			=>	-$ty,
			'tty'			=>	I('post.tty', 0, 'intval'),
			'title'			=>	v_news_cats($ty,'catname'),
			'ftitle'		=>	I('post.ftitle',''),
			'name'   		=>	I('post.name',''),
			'content'		=>	I('post.content',''),
			'content2'		=>	I('post.content2',''),
			'content3'		=>	I('post.content3',''),
			'content4'		=>	I('post.content4',''),
			'content5'		=>	I('post.content5',''),
			'source'		=>	I('post.source',''),
			'linkurl'		=>	I('post.linkurl',''),
			'sendtime'		=>	I('post.sendtime',0,'strtotime'),
		);
		uppro('img1',$fields,'ajax');
		uppro('img2',$fields,'ajax');
		uppro('img3',$fields,'ajax');
		uppro('img4',$fields,'ajax');
		uppro('img5',$fields,'ajax');

		$this->logInsert = '添加单页->'.$fields['title'];
		$this->logUpdate = '编辑单页->'.$fields['title'];

		return $fields;
	}

	public function news_cats()
	{
		$fields=array(
			'pid' 		  => 		I('post.pid',0,'intval'),
			'catname'     => 		I('post.catname','','trim'),
			'catname2'    => 		I('post.catname2','','trim'),
			'tpl'         =>	    I('post.tpl','','trim'),
			'imgsize'     =>	 	I('post.imgsize','','trim'),
			'seotitle'    =>	 	I('post.seotitle','','trim'),
			'keywords'    =>	 	I('post.keywords','','trim'),
			'description' =>	 	I('post.description','','trim'),
			'linkurl'     =>	 	I('post.linkurl','','trim'),
			'contentTemplate'		=>	I('post.contentTemplate',''),
			'weblinkurl'  =>	 	I('post.weblinkurl','','trim'),
			'showtype'    =>	 	I('post.showtype',1,'intval'),
			'disorder'    =>	 	I('post.disorder',0,'intval'),
			'iscats'      =>	 	I('post.iscats',0,'intval'),
            'content'		=>	I('post.content',''),
		);

		uppro('img1',$fields,'ajax');
		uppro('img2',$fields,'ajax');
		uppro('img3',$fields,'ajax');

		$this->logInsert = '添加栏目分类'.$fields['catname'];
		$this->logUpdate = '编辑栏目分类'.$fields['catname'];

		if ($this->isInsert) {
			$fields['isstate']=1;
			$fields['ishidden']=1;
		}

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

}
