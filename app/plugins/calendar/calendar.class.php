<?php
class calendar{
	/**
	 * holds the list of months
	 *
	 * @var array
	 * @access private
	 */
	//private $_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	private $_months = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Deciembre');

	/**
	 * holds the list of days
	 *
	 * @var array
	 * @access private
	 */
	//private $_days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	private $_days = array( 'Domingo','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sábado');

	/**
	 * holds all of the css class definitions
	 *
	 * @var array
	 * @access private
	 */
	private $_class_defintions = array();

	/**
	 * holds the month
	 *
	 * @var integer
	 * @access private
	 */
	private $_month;

	/**
	 * holds the year
	 *
	 * @var integer
	 * @access private
	 */
	private $_year;

	/**
	 * holds data that will be filled in each day
	 * the indexes are zero-keyed
	 *
	 * @var array
	 * @access private
	 */
	private $_day_data = array();

	/**
	 * class constructor
	 *
	 * @param string|integer $month -- defines the month to be used in the calendar
	 * @param integer $year -- defines the year
	 * @param array $day_data -- zero-key index to define the data that will be displayed with each day
	 * @access public
	 * @return void
	 */
	public function __construct($month = false, $year = false, array $day_data = array()){
		$this->setMonth($month)
		->setYear($year)
		->setMultipleDayData($day_data)
		->setDefaultClassDefinitions();
	}

	/**
	 * this method sets the css to be used in the calendar creation see {@link: $_class_definitions} for allowed keys
	 *
	 * @param array $settings
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function setCssDefinitions(array $settings = array()){
		$this->_class_defintions = array_merge($this->_class_defintions, $settings);

		return $this;
	}

	/**
	 * this method sets the month value
	 * it will convert a sting-based value to an integer one
	 * if no month is passed in, it will use the current month
	 *
	 * @param string|integer $month -- defines the month to be used in the calendar
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function setMonth($month = false){
		if(!(bool) $month){
			$month = date('n', time());
		}elseif(!is_int($month)){
			foreach($this->_months as $key => $name){
				if(preg_match('/^'. $month .'/i', $name)){
					$month = $key + 1;

					break;
				}
			}
		}

		$this->_month = $month;

		return $this;
	}

	/**
	 * sets the year
	 * if no year is passed it, it will use the current year
	 *
	 * @param integer $year
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function setYear($year){
		if(!isset($year)){
			$year = date('Y', time());
		}

		$this->_year = $year;

		return $this;
	}

	/**
	 * method used to assign how the days of the week will be displayed
	 *
	 * @param array $days
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function setDaysOfWeek(array $days = array()){
		$this->_days = $days;

		return $this;
	}

	/**
	 * sets the data for each day of the month
	 * uses a zero key index
	 *
	 * @param array $data -- the key is the day (actual day minus one) the value is the data to be passed in that day
	 * @param boolean $append -- this will say to overwrite the day or not with the data. Defaults to true
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function setMultipleDayData(array $data = array(), $append = true){
		if(count($data)){
			foreach($data as $day => $content){
				$this->setDayData($day, $content, $append);
			}
		}

		return $this;
	}

	/**
	 * sets the data for a given day
	 *
	 * @param integer $day -- the zero key of the day to be modified
	 * @param mixed $content -- the content for that day
	 * @param boolean $append -- this will say to overwrite the day or not with the data. Defaults to true
	 * @access public
	 * @return return type
	 */
	public function setDayData($day, $content, $append = true){
		$current_content       = isset($this->_day_data[$day]) ? $this->_day_data[$day] : '';
		$this->_day_data[$day] = $append ? $current_content . $content : $content;

		return $this;
	}

	/**
	 * this creates the calendar
	 *
	 * @access public
	 * @return string
	 */
	public function render(){
		$running_day       = date('w', mktime(0, 0, 0, $this->_month, 1, $this->_year));
		$days_in_month     = date('t', mktime(0, 0, 0, $this->_month, 1, $this->_year));
		$days_in_this_week = 1;
		$day_counter       = 0;
		$rows              = array();
		$row_count         = 6;
		$day_head          = $this->dayHead();
		$month_head        = $this->monthHead();
		$cells             = '';

		/**
		 * create the leading empty cells
		 */
		for($x = 0; $x < $running_day; $x++){
			$cells .= $this->day(false);

			$days_in_this_week++;
		}
			
		/**
		 * creates the rest of the days for the week
		 * if it is the last day of the week, wrap the cells in a row
		 */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++){
			$cells .= $this->day($list_day);

			if($running_day == 6){
				$rows[]            = $this->row($cells, $this->_class_defintions['row']);
				$cells             = '';
				$running_day       = -1;
				$days_in_this_week = 0;

				$row_count--;
			}

			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		}

			
		/**
		 * build the trailing empty days
		 */
		if($days_in_this_week < 8){
			for($x = 1; $x <= (8 - $days_in_this_week); $x++){
				$cells .= $this->day(false);
			}

			$row_count--;
		}
			
		$rows[] = $this->row($cells, $this->_class_defintions['row']);
			
		/**
		 * if there are still rows left, create an empty row
		 */
		if($row_count > 0){
			$empty = '';

			for($x = 1; $x <= 7; $x++){
				$empty .= $this->day(false);
			}

			$rows[] = $this->row($empty, $this->_class_defintions['row']);
		}

		return utf8_encode($this->table($month_head . $day_head . implode('', $rows)));
	}

	/**
	 * resets the class so that the same instance can be used to create another month
	 *
	 * @access public
	 * @return System_Utility_Calendar
	 */
	public function reset(){
		$this->_month    = false;
		$this->_year     = false;
		$this->_day_data = array();

		return $this->setDefaultClassDefinitions();
	}

	/**
	 * method used to create the outlining table
	 *
	 * @param string $content -- the rows and cells
	 * @access private
	 * @return string
	 */
	private function table($content){
		return '<table class="calendar" cellspacing="0" cellpadding="0" >
                <tbody>
                    '. $content .'
                </tbody>
            </table>
        ';
	}

	/**
	 * method used to display the month heading
	 *
	 * @access private
	 * @return string
	 */
	private function monthHead(){
		
		$pyear = $this->_year;
		$nyear = $this->_year;
		$prev_month = $this->_month - 1;
		$next_month = $this->_month + 1;
		
		if($prev_month==0){ $pyear = $pyear-1; $prev_month=12;}
	    if($next_month==13){ $nyear = $nyear+1; $next_month=1;}
		
		
		$html .=' '.$this->_months[($this->month-1)].', '.$this->year.' <a href="#" title="next month" class="nav">&raquo;</a></caption>';
		return '<caption><a href="?month='.$prev_month.'&year='.$pyear.'" title="Previous month" class="nav">&laquo;</a>'. $this->_months[($this->_month - 1)] .', '.$this->_year.' <a href="?month='.$next_month.'&year='.$nyear.'"  title="next month" class="nav">&raquo;</a></td>';
		
	}

	/**
	 * method used to create the day heading
	 *
	 * @access private
	 * @return string
	 */
	private function dayHead(){
		foreach ($this->_days as $day){
		 $days[] = substr($day, 0,3);
		}
		
		
		return '
            <tr class="'. $this->_class_defintions['row'] .'">
                <th class="'. $this->_class_defintions['heading'] .'">'. implode('</th><th class="'. $this->_class_defintions['heading'] .'">', $days). '</th>
            </tr>
        ';
	}

	/**
	 * this method will build out a day in the calendar
	 * if false is passed in for the day, an empty day cell will be returned
	 *
	 * @param integer|booean $day -- the current day of the month that is being built
	 * @access private
	 * @return string
	 */
	private function day($day){
		if((bool) $day){
			$class   = $this->_class_defintions['working_day'];
			$content = isset($this->_day_data[($day)]) ? $this->_day_data[($day)] : '';
			$day_num = '<div class="'. $this->_class_defintions['day_number'] .'">'. $day .'</div>';

			/**
			 * if there is content, set the class to whatever is content_day
			 */
			if($content !== ''){
				$class = $this->_class_defintions['content_day'];
			}
		}else{
			$class   = $this->_class_defintions['blank_day'];
			$content = '';
			$day_num = '';
		}

		return '
            <td class="'. $class. '">
                <div class="calendar_day_container">
                    '. $day_num . $content .'
                </div>
            </td>
        ';
	}

	/**
	 * method creates a week row
	 *
	 * @param string $content -- the cells that will fill the row
	 * @param string $class -- the class to be used on the row
	 * @access private
	 * @return string
	 */
	private function row($content, $class = ''){
		return '
            <tr class="'. $class .'">
                '. $content .'
            </tr>
        ';
	}

	/**
	 * method used to define the default class names
	 *
	 * @access private
	 * @return System_Utility_Calendar
	 */
	private function setDefaultClassDefinitions(){
		return $this->setCssDefinitions(array(
            'blank_day'   => 'calendar-day-np',
            'working_day' => 'calendar-day',
            'content_day' => 'calendar-day-content',
            'day_number'  => 'day-number',
            'row'         => 'calendar-row',
            'heading'     => 'calendar-day-head',
            'calendar'    => 'calendar'  
            ));
	}
}
