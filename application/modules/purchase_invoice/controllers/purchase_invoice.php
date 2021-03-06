<?asp
class Purchase_invoice extends MX_Controller{
   function __construct() {
                parent::__construct();
                $this->load->library('posnic');               
    }
    function index(){     
        $this->load->view('template/app/header'); 
        $this->load->view('header/header');         
        $this->load->view('template/branch',$this->posnic->branches());
        $data['active']='purchase_invoice';
        $this->load->view('index',$data);
        $this->load->view('template/app/navigation',$this->posnic->modules());
        $this->load->view('template/app/footer');
        
    }
    // goods Receiving Note data table
    function data_table(){
        $aColumns = array( 'guid','invoice','grn_no','c_name','s_name','date','invoice','invoice','invoice','invoice','guid' );	
	$start = "";
	$end="";
        if ( $this->input->get_post('iDisplayLength') != '-1' )	{
                $start = $this->input->get_post('iDisplayStart');
                $end=	 $this->input->get_post('iDisplayLength');              
        }	
        $order="";
        if ( isset( $_GET['iSortCol_0'] ) )
            {	
                for ( $i=0 ; $i<intval($this->input->get_post('iSortingCols') ) ; $i++ )
                {
                    if ( $_GET[ 'bSortable_'.intval($this->input->get_post('iSortCol_'.$i)) ] == "true" )
                    {
                        $order.= $aColumns[ intval( $this->input->get_post('iSortCol_'.$i) ) ]." ".$this->input->get_post('sSortDir_'.$i ) .",";
                    }
                }
                $order = substr_replace( $order, "", -1 );


        }
	$like = array();
	if ( $_GET['sSearch'] != "" )
            {
                $like =array(
                    'po_no'=>  $this->input->get_post('sSearch'),
                    'grn_no'=>  $this->input->get_post('sSearch'),
                    );

            }
            $this->load->model('invoice')	   ;
            $rResult1 = $this->invoice->get($end,$start,$like,$this->session->userdata['branch_id']);
            $iFilteredTotal =$this->invoice->count($this->session->userdata['branch_id']);
            $iTotal =$iFilteredTotal;
            $output1 = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		foreach ($rResult1 as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] == "id" )
				{
					$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
				}
				else if ( $aColumns[$i]== 'po_date' )
				{
					/* General output */
					$row[] = date('d-m-Y',$aRow[$aColumns[$i]]);
				}
				else if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					$row[] = $aRow[$aColumns[$i]];
				}
				
			}
				
		$output1['aaData'][] = $row;
		}
            echo json_encode($output1);
    }
 
function save(){      
     if($this->session->userdata['purchase_invoice_per']['add']==1){
        $this->form_validation->set_rules('goods_receiving_note_guid',$this->lang->line('goods_receiving_note_guid'), 'required');
        $this->form_validation->set_rules('grn_date',$this->lang->line('grn_date'), 'required');
        $this->form_validation->set_rules('supplier_id',$this->lang->line('supplier_id'), 'required');
        $this->form_validation->set_rules('invoice_no', $this->lang->line('invoice_no'), 'required');
            if ( $this->form_validation->run() !== false ) {    
                $grn=  $this->input->post('goods_receiving_note_guid');
                $date=strtotime($this->input->post('grn_date'));
                $invoice_no= $this->input->post('invoice_no');
                $supplier_id= $this->input->post('supplier_id');
                $remark=  $this->input->post('remark');
                $note=  $this->input->post('note');
                 $po= $this->input->post('purchase_order');
                $this->load->model('invoice');
                 if($po=="" Or $po==NULL) {
                    $po="non";
                 }
                 $where=array('invoice'=>$invoice_no);
                if($this->invoice->check_duplicate($where)){
                $value=array('supplier_id'=>$supplier_id,'invoice'=>$invoice_no,'po'=>$po,'grn'=>$grn,'date'=>$date,'remark'=>$remark,'note'=>$note);
              $guid= $this->posnic->posnic_add_record($value,'purchase_invoice');
                   if($po=='non') {
                    $po="non";
                  
                    $this->invoice->direct_grn_invoice_status($grn);
                    $this->invoice->direct_grn_payable_amount($grn,$guid);
                }else{
                    
                    $this->invoice->grn_invoice_status($grn);
                    $this->invoice->grn_payable_amount($grn,$guid,$po);
                }
                $this->posnic->posnic_master_increment_max('purchase_invoice')  ;
           
                echo 'TRUE';
                }else{
                    echo 'ALREADY';
                }
                }else{
                   echo 'FALSE';
                }
        }else{
                   echo 'Noop';
                }
           
    }
   
        
        
    
    function search_grn_order(){
            $search= $this->input->post('term');
            $this->load->model('invoice');
            $data= $this->invoice->search_grn_order($search,$this->session->userdata['branch_id'])    ;
            echo json_encode($data);
    }
   
    function  get_grn($guid){
        if($this->session->userdata['purchase_invoice_per']['add']==1){
            $this->load->model('invoice');
            $data=  $this->invoice->get_goods_receiving_note($guid);
            echo json_encode($data);
        }
    }
    function  get_direct_grn($guid){
        if($this->session->userdata['purchase_invoice_per']['add']==1){
            $this->load->model('invoice');
            $data=  $this->invoice->get_direct_grn($guid);
            echo json_encode($data);
        }
    }
    function  get_goods_receiving_note($guid){
        if($this->session->userdata['purchase_order_per']['edit']==1){
        $this->load->model('invoice');
        $data=  $this->invoice->get_goods_receiving_note($guid);
        echo json_encode($data);
        }
    }
   
   
    function order_number(){
           $data[]= $this->posnic->posnic_master_max('purchase_invoice')    ;
           echo json_encode($data);
    }
    function search_items(){
           $search= $this->input->post('term');
           $guid= $this->input->post('suppler');
             if($search!=""){
                $this->load->model('purchase');
                $data= $this->purchase->serach_items($search,$this->session->userdata['branch_id'],$guid);      
                echo json_encode($data);
            }

    }
    function language($lang){
       $lang= $this->lang->load($lang);
       return $lang;
    }
    // get grn data
   
    }
?>
