
<script type="text/javascript" charset="utf-8">
          $(document).ready( function () {
           
                    $('#add_item_form').hide();
                    $('#edit_item_form').hide();
                              posnic_table();
                                add_item.onsubmit=function()
                                { 
                                  return false;
                                } 
                                parsley_reg.onsubmit=function()
                                { 
                                  return false;
                                } 
                         
                        } );
                        
           function posnic_table(){
           $('#dt_table_tools').dataTable({
                                      "bProcessing": true,
				      "bServerSide": true,
                                      "sAjaxSource": "<?php echo base_url() ?>index.php/items/data_table",
                                       aoColumns: [  
                                    
         { "bVisible": false} , {	"sName": "ID",
                   						"bSearchable": false,
                   						"bSortable": false,
                                                                
                   						"fnRender": function (oObj) {
                   							return "<input type=checkbox value='"+oObj.aData[0]+"' >";
								},
								
								
							},
        
        null, null,  null, null,  null,null, 

 							{	"sName": "ID",
                   						"bSearchable": false,
                   						"bSortable": false,
                                                                
                   						"fnRender": function (oObj) {
                   							if(oObj.aData[9]==0){
                                                                            return '<span data-toggle="tooltip" class="label label-success hint--top hint--success" ><?php echo $this->lang->line('active') ?></span>';
                                                                        }else{
                                                                            return '<span data-toggle="tooltip" class="label label-danger hint--top data-hint="<?php echo $this->lang->line('active') ?>" ><?php echo $this->lang->line('deactive') ?></span>';
                                                                        }
								},
								
								
							},
 							{	"sName": "ID1",
                   						"bSearchable": false,
                   						"bSortable": false,
                                                                
                   						"fnRender": function (oObj) {
                                                                if(oObj.aData[5]==0){
                   							return '<a href=javascript:posnic_deactive("'+oObj.aData[2]+'","'+oObj.aData[0]+'")><span data-toggle="tooltip" class="label label-warning hint--top hint--warning" data-hint="<?php echo $this->lang->line('deactive') ?>"><i class="icon-pause"></i></span></a>&nbsp<a href=javascript:edit_function("'+oObj.aData[0]+'")  ><span data-toggle="tooltip" class="label label-info hint--top hint--info" data-hint="EDIT"><i class="icon-edit"></i></span></a>'+"&nbsp;<a href=javascript:user_function('"+oObj.aData[2]+"','"+oObj.aData[0]+"'); ><span data-toggle='tooltip' class='label label-danger hint--top hint--error' data-hint='DELETE'><i class='icon-trash'></i></span> </a>";
								}else{
                                                                        return '<a href=javascript:posnic_active("'+oObj.aData[2]+'","'+oObj.aData[0]+'") ><span data-toggle="tooltip" class="label label-success hint--top hint--success" data-hint="<?php echo $this->lang->line('active') ?>"><i class="icon-play"></i></span></a>&nbsp<a href=javascript:edit_function("'+oObj.aData[0]+'") ><span data-toggle="tooltip" class="label label-info hint--top hint--info" data-hint="EDIT"><i class="icon-edit"></i></span></a>'+"&nbsp;<a href=javascript:user_function('"+oObj.aData[2]+"','"+oObj.aData[0]+"'); ><span data-toggle='tooltip' class='label label-danger hint--top hint--error' data-hint='DELETE'><i class='icon-trash'></i></span> </a>";
                                                                }
                                                                },
								
								
							},

 							

 						]
		}
						
						
                                    
                                    );
                                   
			}
    function user_function(items,guid){
    <?php if($_SESSION['items_per']['delete']==1){ ?>
             bootbox.confirm("Are you Sure To Delete This Items ("+items+")", function(result) {
             if(result){
            $.ajax({
                url: '<?php echo base_url() ?>/index.php/items/delete',
                type: "POST",
                data: {
                    guid: guid
                    
                },
                success: function(response)
                {
                    if(response){
                          bootbox.alert('User '+items+' Is Deleted');
                        $("#dt_table_tools").dataTable().fnDraw();
                    }}
            });
        

                        }
    }); <?php }else{?>
           bootbox.alert("<?php echo $this->lang->line('You Have NO permission To Delete This Records') ?>");
   <?php }
?>
                        }
            function posnic_deactive(user,guid){
                $.ajax({
                url: '<?php echo base_url() ?>index.php/items/deactive',
                type: "POST",
                data: {
                    guid: guid
                    
                },
                success: function(response)
                {
                    if(response){
                         $.bootstrapGrowl(user+'<?php echo $this->lang->line('isdeactivated');?>', { type: "danger" });
                        $("#dt_table_tools").dataTable().fnDraw();
                    }
                }
            });
            }
            function posnic_active(user,guid){
                           $.ajax({
                url: '<?php echo base_url() ?>index.php/items/active',
                type: "POST",
                data: {
                    guid: guid
                    
                },
                success: function(response)
                {
                    if(response){
                         $.bootstrapGrowl(user+'<?php echo $this->lang->line('isactivated');?>', { type: "success" });
                        $("#dt_table_tools").dataTable().fnDraw();
                    }
                }
            });
            }
           function edit_function(guid){
                       $("#parsley_reg").trigger('reset');
                        <?php if($_SESSION['items_per']['edit']==1){ ?>
                            $.ajax({                                      
                             url: "<?php echo base_url() ?>index.php/items/edit_items/"+guid,                      
                             data: "", 
                             dataType: 'json',               
                             success: function(data)        
                             {    
                                 $("#user_list").hide();
                                 $('#edit_item_form').show('slow');
                                 $('#delete').attr("disabled", "disabled");
                                 $('#posnic_add_items').attr("disabled", "disabled");
                                 $('#active').attr("disabled", "disabled");
                                 $('#deactive').attr("disabled", "disabled");
                                 $('#items_lists').removeAttr("disabled");
                                 $('#parsley_reg #guid').val(data[0]['guid']);
                                 $('#parsley_reg #name').val(data[0]['name']);
                                 $('#parsley_reg #sku').val(data[0]['code']);
                                 $('#parsley_reg #barcode').val(data[0]['barcode']);
                                 $('#parsley_reg #description').val(data[0]['description']);
                                 $('#parsley_reg #cost').val(data[0]['cost_price']);
                                 $('#parsley_reg #selling_price').val(data[0]['selling_price']);
                                 $('#parsley_reg #mrp').val(data[0]['mrp']);
                                 $('#parsley_reg #discount_per').val(data[0]['discount_amount']);
                                 $('#parsley_reg #starting_date').val(data[0]['start_date']);
                                 $('#parsley_reg #ending_date').val(data[0]['end_date']);
                                 $('#parsley_reg #tax_Inclusive').val(data[0]['tax_Inclusive']);
                                 $('#parsley_reg #location').val(data[0]['location']);
                                 $('#parsley_reg #category').val(data[0]['category_id']);
                                 //$('#parsley_reg #search_category').val(data[0]['c_guid']);
                                
                                $("#parsley_reg #search_category").select2('data', {id:data[0]['c_guid'],text: data[0]['c_name']});
                                $('#parsley_reg #category').val(data[0]['c_guid']);
                                
                                $("#parsley_reg #search_brand").select2('data', {id:data[0]['b_guid'],text: data[0]['b_name']});
                                $('#parsley_reg #brand').val(data[0]['b_guid']);
                                
                                $("#parsley_reg #search_department").select2('data', {id:data[0]['d_guid'],text: data[0]['department_name']});
                                $('#parsley_reg #department').val(data[0]['d_guid']);
                                
                                $("#parsley_reg #search_supplier").select2('data', {id:data[0]['supplier_id'],text: data[0]['company_name'],first: data[0]['s_first_name'],phone: data[0]['s_phone'],email: data[0]['s_email']});
                                $('#parsley_reg #supplier').val(data[0]['s_guid']);
                                
                                $("#parsley_reg #search_taxes_area").select2('data', {id:data[0]['tax_area_id'],text: data[0]['area_name']});
                                $('#parsley_reg #taxes_area').val(data[0]['tax_area_id']);
                                
                                $("#parsley_reg #search_taxes").select2('data', {id:data[0]['tax_id'],text: data[0]['type'],value:data[0]['value']});
                                $('#parsley_reg #taxes').val(data[0]['tax_id']);
                                
//                                 $('#parsley_reg #tax_Inclusive').val(data[0]['tax_Inclusive']);
//                                 $('#parsley_reg #tax_Inclusive').val(data[0]['tax_Inclusive']);
                                 
                               
                             } 
                           });
                         
                        
                              
                         
                        <?php }else{?>
                                bootbox.alert("<?php echo $this->lang->line('You Have NO permission To Edit This Records') ?>");
                        <?php }?>
                       }
		</script>
                <script type="text/javascript" charset="utf-8" language="javascript" src="<?php echo base_url() ?>template/data_table/js/DT_bootstrap.js"></script>


  