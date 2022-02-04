<?php
/** 
 * @author jguerrero
 * 
 */
class SistemaMenus {
	protected $blank1stItem=false;
	protected $multiple_select=false;
	protected $size=0;
	protected $Atributos;
	protected $class="";
	protected $requerdio=false;
	protected $desabilitado=false;
	protected $data=array();
	
	protected $vacio=false;

	function __construct() {
		
	}
	
	/**
	 * 
	 * @param int $cve_sistema_bachillerato
	 * @param string $nombre
	 * @return boolean
	 */
	

	public function ConceptoGrupo($cve_grupo,$cve_concepto=0,$nombre="cve_concepto"){
		$query="select nombre,cve_concepto from dbo.concepto where cve_grupo_concepto=$cve_grupo order by 1";
		return $this->generarMenu($query, $nombre, $cve_concepto);
	}
	
	public function generarMenu($query,$nombre, $id){
		$db=&get_instance()->db;
		//$db->debug=true;	
		$rs=$db->Execute($query);
		$this->setAtributos();
		$this->data=array();
		if($rs!==false && $rs->RecordCount()>0){
			return @$rs->getMenu2($nombre,$id,$this->blank1stItem,$this->multiple_select,$this->size,$this->Atributos);
		}elseif($this->vacio){
			
			return $this->generarMenuVacio($nombre);
		}
		//limpiamos los atributos data;
		
		return false;
	}
	
	public function generarMenuVacio($nombre){
		$html="<select name=\"$nombre\"";
		if($this->multiple_select){	$html.= ' multiple="multiple"';}
		if($this->size>0){	$html.=" size=\"$this->size\"";}
		$html.= " {$this->Atributos}>";
		if($this->blank1stItem){
			$html.="<option></option>";
		}
		$html.="</select>";
		return $html;
	}
	
	/**
	 * @return the $blank1stItem
	 */
	public function getBlank1stItem() {
		return $this->blank1stItem;
	}

	/**
	 * @return the $multiple_select
	 */
	public function getMultiple_select() {
		return $this->multiple_select;
	}

	/**
	 * @return the $size
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return the $class
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @param boolean $blank1stItem
	 */
	public function setBlank1stItem($blank1stItem) {
		$this->blank1stItem = $blank1stItem;
	}

	/**
	 * @param boolean $multiple_select
	 */
	public function setMultiple_select($multiple_select) {
		$this->multiple_select = $multiple_select;
	}

	/**
	 * @param number $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * @param string $class
	 */
	public function setClass($class) {
		$this->class = $class;
		
		
	}
	
	protected function setAtributos(){
		$this->Atributos="";
		if($this->class!=""){
			$this->Atributos.=" class=\"$this->class\"";
		}		
		if($this->requerdio){
			$this->Atributos.=" required=\"required\"";
		}
		if($this->desabilitado){
			$this->Atributos.=" disabled=\"disabled\"";
		}
		if(count($this->data)>0){
			foreach ($this->data as $data=>$val){
				$this->Atributos.=" data-$data=\"$val\" ";
			}
			
		}
		
	}
	/**
	 * @return the $requerdio
	 */
	public function getRequerdio() {
	}

	/**
	 * @param boolean $requerdio
	 */
	public function setRequerdio($requerdio) {
		$this->requerdio = $requerdio;
		$this->setAtributos();
	}
	/**
	 * @return the $data
	 */
	public function getData($name) {
		return $this->data[$name];
	}

	/**
	 * @param multitype: $data
	 */
	public function setData($name, $value) {
		
		$this->data[$name] = $value;
		
		return  $this;
	}
	/**
	 * @return the $desabilitado
	 */
	public function getDesabilitado() {
		return $this->desabilitado;
	}

	/**
	 * @param boolean $desabilitado
	 */
	public function setDesabilitado($desabilitado) {
		$this->desabilitado = $desabilitado;
		$this->setAtributos();
	}
	public function getVacio() {
		return $this->vacio;
	}

	public function setVacio($vacio) {
		$this->vacio = $vacio;
	}

	
	public Function Numerico($nombre,$incio,$fin,$default=null,$intervalo=1){
		
		$html="<select name=\"$nombre\"";
		if($this->multiple_select){	$html.= ' multiple="multiple"';}
		if($this->size>0){	$html.=" size=\"$this->size\"";}
		$html.= " {$this->Atributos}>";
		if($this->blank1stItem){
			$html.="<option></option>";
		}
		if($incio>$fin){
			for($v=$incio;$v<=$fin;$v+=$intervalo){
				$chk=($default!==null && $default==$v)?"selected":"";
				$html.="<option value=\"$v\" $chk>$v</option>";
			}
		}else{
		for($v=$incio;$v<=$fin;$v+=$intervalo){
				$chk=($default!==null && $default==$v)?"selected":"";
				
				$html.="<option value=\"$v\" $chk>$v</option>";
			}
		}
		$html.="</select>";
		return $html;
	}

}

?>