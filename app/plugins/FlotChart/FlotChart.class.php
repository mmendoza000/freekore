<?php
/**
 *
 * @method construct
 * @example $Chart = new FlotChart([pie|bar]);
 */
class FlotChart
{

	var $data = array();
	var $data_index = 0;
	var $uniq_id = 0;
	var $type = '';
	var $widthStyle = '300px';
	var $heightStyle = '400px';

	public function __construct($type){

		$this->type = $type;

	}

	/**
	 * @method AddData
	 * @desc sets the data
	 */

	public function AddData($Label,$Value){

		$this->data[$this->data_index]['label'] = $Label;
		$this->data[$this->data_index]['data'] = $Value;

		$this->data_index ++;
	}
	public function AddBarData($Label,$x,$y){
		
		$this->data[$this->data_index]['label'] = $Label;
		$this->data[$this->data_index]['data'] = array(array($x,$y));
		$this->data[$this->data_index]['bars'] = array('show'=>'true');
	            	
		$this->data_index ++;
		
	}
	/**
	 * @method Render
	 * @desc Returns the Chart
	 */

	public function Render($widthStyle,$heightStyle='300px'){
		
		$this->widthStyle = $widthStyle;
		$this->heightStyle = $heightStyle;

		$this->uniq_id = 'chrt-'.uniqid();
		$this->data =  json_encode($this->data);
		
		if($this->type=='pie'){
			$this->pieChart();
		}elseif ($this->type='bar'){
			$this->barChart();
				
		}

		


	}

	private function pieChart(){
		?>
<script type="text/javascript">
$(function () {
	
	var data = <?php echo $this->data;?>;

	// DEFAULT
    $.plot($("#<?php echo $this->uniq_id?>"), data, 
    		{
    		        series: {
    		            pie: { 
    		                show: true,
    		                radius: 1,
    		                tilt: 0.5,
    		                label: {
    		                    show: true,
    		                    radius: 0.90,
    		                    formatter: function(label, series){
    		                        return '<div class="datalabel">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
    		                    },
    		                    background: { opacity: 0.6 }
    		                }
    		            }
    		        },
    		        legend: {
    		            show: false
    		        }
    		});
	
	

});

</script>
<div
	id="<?php echo $this->uniq_id;?>" class="graph"
	style="width:<?php echo $this->widthStyle?>; height:<?php echo $this->heightStyle?>;"></div>
		<?php

	}

	private function barChart(){
		
		
		?>
		
<script type="text/javascript">
$(function () {
	

    var data = <?php echo $this->data?>;

    var options = {
            legend: { noColumns: 3 },
            xaxis: { tickDecimals: 0 },
            yaxis: { min: 0 },
        };
                        
    $.plot($("#<?php echo $this->uniq_id?>"), data,options);
	
	

});

</script>
<h1>Flot Pie Examples</h1>

<h2>Ventas por Agente</h2>
<div
	id="<?php echo $this->uniq_id;?>" class="graph"
	style="width:<?php echo $this->widthStyle?>; height:<?php echo $this->heightStyle?>;"></div>
		<?php

	}


} // End class