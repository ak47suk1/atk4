<?php
/*
   This class represents sequentall calls to one jQuery object
   */
class jQuery_Chain extends AbstractModel {
	private $str='';
	private $prepend='';
	private $selector=false;
	function __call($name,$arguments){
		if($arguments){
			$a2=array();
			foreach($arguments as $arg){
				if(is_object($arg)){
					if($arg instanceof jQuery_Chain){
						$s="function(){ ".$arg->_render()." }";
					}else{
						$s="'#".$arg->name."'";
					}
				}elseif(is_int($arg)){
					$s="$arg";
				}elseif(is_array($arg)){
					$s=json_encode($arg);
				}elseif(is_bool($arg)){
					$s=$arg?"true":"false";
				}elseif(is_string($arg)){
					$s="'".addslashes($arg)."'";
				}else{
					throw new BaseException("wrong argument type to jQuery_Chain: ".$arg );
				}
				$a2[]=$s;
			}
			$this->str.=".$name(".join(",",$a2).")";
		}else{
			$this->str.=".$name()";
		}
		return $this;
	}
	function _fn($name,$arguments=array()){
		// Wrapper for functons which use reserved words
		return $this->__call($name,$arguments);
	}
	function _selector($selector){
		$this->selector=$selector;
		return $this;
	}
	function _prepend($code){
		$this->prepend=$code.';'.$this->prepend;
		return $this;
	}
	function _render(){
		$ret=$this->prepend;
		if($this->str)$ret.="$('".($this->selector?$this->selector:'#'.$this->owner->name)."')";
		$ret.=$this->str;
		return $ret;
	}
	function _load($file){
		$this->api->jquery->addInclude($file);
		return $this;
	}
	function render(){
		$this->output($this->base.$this->str.";\n");
	}
}