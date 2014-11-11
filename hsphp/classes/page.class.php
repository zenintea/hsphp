<?php
	!defined('ZF') && exit('Access Denied');
	class page {
		private $total; //数据表中总记录数
		private $listRows; //每页显示行数
		private $limit;
		private $uri;
		private $pageNum; //页数
		private $config=array('header'=>"个记录", "prev"=>"上一页", "next"=>"下一页", "first"=>"首 页", "last"=>"尾 页");
		private $listNum=12;
		private $name=''; //锚点
		/*
		 * $total 
		 * $listRows
		 */
		public function __construct($total, $listRows=10, $name='',$pa=""){
			$this->total=$total;
			$this->listRows=$listRows;
			$this->uri=$this->getUri($pa);
			$this->name=$name;
			$this->page=!empty($_GET["page"]) ? $_GET["page"] : 1;
			$this->pageNum=ceil($this->total/$this->listRows);
			$this->limit=$this->setLimit();
		}

		private function setLimit(){
			return "Limit ".($this->page-1)*$this->listRows.", {$this->listRows}";
		}

		private function getUri($pa){
			$url=$_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"], '?')?'':"?").$pa;
			$parse=parse_url($url);

			if(isset($parse["query"])){
				parse_str($parse['query'],$params);
				unset($params["page"]);
				$url=$parse['path'].'?'.http_build_query($params);
				
			}

			return $url;
		}

		private function __get($args){
			if($args=="limit")
				return $this->limit;
			else
				return null;
		}

		private function start(){
			if($this->total==0)
				return 0;
			else
				return ($this->page-1)*$this->listRows+1;
		}

		private function end(){
			return min($this->page*$this->listRows,$this->total);
		}

		private function first(){
			if($this->page==1)
				$html.='';
			else
				$html.="&nbsp;&nbsp;<a class='btn_a' href='{$this->uri}&page=1{$this->name}'><span>{$this->config['first']}</span></a>&nbsp;&nbsp;";

			return $html;
		}

		private function prev(){
			if($this->page==1)
				$html.='';
			else
				$html.="&nbsp;&nbsp;<a class='btn_a' href='{$this->uri}&page=".($this->page-1)."{$this->name}'><span>{$this->config['prev']}</span></a>&nbsp;&nbsp;";

			return $html;
		}

		private function pageList(){
			$linkPage="";
			
			$inum=floor($this->listNum/2);
		
			for($i=$inum; $i>=1; $i--){
				$page=$this->page-$i;

				if($page<1)
					continue;

				$linkPage.="&nbsp;<a href='{$this->uri}&page={$page}{$this->name}'>{$page}</a>&nbsp;";

			}
		
			$linkPage.="&nbsp;<b style=\"background:#89B001;border-radius: 3px; padding:3px 5px;\">{$this->page}</b>&nbsp;";
			

			for($i=1; $i<=$inum; $i++){
				$page=$this->page+$i;
				if($page<=$this->pageNum)
					$linkPage.="&nbsp;<a href='{$this->uri}&page={$page}{$this->name}'>{$page}</a>&nbsp;";
				else
					break;
			}

			return $linkPage;
		}

		private function next(){
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="&nbsp;&nbsp;<a class='btn_a' href='{$this->uri}&page=".($this->page+1)."{$this->name}'><span>{$this->config["next"]}</span></a>&nbsp;&nbsp;";

			return $html;
		}

		private function last(){
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="&nbsp;&nbsp;<a class='btn_a' href='{$this->uri}&page=".($this->pageNum)."{$this->name}'><span>{$this->config["last"]}</span></a>&nbsp;&nbsp;";

			return $html;
		}

		private function goPage(){
			return '&nbsp;&nbsp;<input type="text" style="height:16px;width:20px;line-height:16px"  onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;location=\''.$this->uri.'&page=\'+page+\'\'}" value="'.$this->page.'" style="width:25px"><input style="line-height:14px; height:22px; margin-left:10px; width:40px" type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;location=\''.$this->uri.'&page=\'+page+\'\'">&nbsp;&nbsp;';
		}
		function fpage($display=array(0,1,2,3,4,5,6,7,8)){
			$html[0]="共有{$this->total}{$this->config['header']},";
			$html[1]="每页显示".($this->end()-$this->start()+1)."条,本页显示{$this->start()}-{$this->end()},条";
			$html[2]="<b>{$this->page}/{$this->pageNum}</b>页&nbsp;";	
			$html[3]=$this->first();
			$html[4]=$this->prev();
			$html[5]=$this->pageList();
			$html[6]=$this->next();
			$html[7]=$this->last();
			$html[8]=$this->goPage();
			$fpage='';
			foreach($display as $index){
				$fpage.=$html[$index];
			}

			return $fpage;

		}

	
	}
