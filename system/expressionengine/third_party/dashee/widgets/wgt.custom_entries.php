<?php

/**
 * Custom Entries Widget
 *
 * Display customized listing of Channel Entries.
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Widget
 * @author		John Henry Donovan
 * @link		http://johnhenry.ie
 * @version		1.0
 */

class Wgt_custom_entries
{
	public $widget_name 		= 'Custom Entries';
	public $widget_description 	= 'Displays X number of recent entries from chosen channel.';

	public $title;
	public $wclass;
	public $settings;
	
	private $_EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{


		$this->settings = array(
			'title' => 'Channel Name Entries',
			'channel_id' => '1',
			'limit' => '10',
			'comments' => 'no'
			);
		$this->wclass = 'contentMenu';
			
		$this->_EE =& get_instance();
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	string
	 */
	public function index($settings = NULL)
	{
	
		$this->title = $settings->title;
	
		// get most recent 10 entries from DB
		$this->_EE->db->select('entry_id, channel_id, title, entry_date, status, comment_total');
		$this->_EE->db->from('channel_titles');
		$this->_EE->db->where('channel_titles.channel_id',$settings->channel_id);
		$this->_EE->db->order_by('entry_date DESC');
		$this->_EE->db->limit($settings->limit);
		$entries = $this->_EE->db->get();
	
		// generate table HTML
		$display = '';
		$comments_th = '';
		
		if($entries->num_rows() > 0)
		{
			foreach($entries->result() as $entry)
			{
				$display .= '
					<tr class="'.alternator('odd','even').'">
						<td><a href="'.BASE.AMP.'D=cp'.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id='.$entry->channel_id.AMP.'entry_id='.$entry->entry_id.'">'.$entry->title.'</a></td>
						<td>'.date('d/m/Y',$entry->entry_date).'</td>
						<td><span class="status_'.$entry->status.'">'.$entry->status.'</span></td>';
				// Check for Comment Column		
				if ($settings->comments  == "yes"){
					$comments_th ='<th>Comments</th>';
					$display .= '<td><a href="'.BASE.AMP.'D=cp'.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=comment'.AMP.'method=index'.AMP.'entry_id='.$entry->entry_id.'">'.$entry->comment_total.'</a></td>';
						};
						
					$display .= '</tr>';
			}
		}
		else
		{
			$display = '<tr><td colspan="3"><center>No entries have been created.</center></td></tr>';
		}
		

		
		return '
		<div style="margin:5px;float:right;">
			<a class="submit submit_alt" href="'.BASE.AMP.'C=content_edit'.AMP.'channel_id='.$settings->channel_id.'">View All</a>
			<a class="submit submit_alt" href="'.BASE.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id='.$settings->channel_id.'">Add New</a>
		</div>
			<table>
				<thead>
				<tr>
					<th>Title</th>
					<th>Entry Date</th>
					<th>Status</th>'
					.$comments_th.
				'</tr></thead>
				<tbody>'.$display.'</tbody>
			</table>
		';
	}
	/**
	 * Settings Form Function
	 * Generate settings form for widget.
	 *
	 * @param	object
	 * @return 	string
	 */
	public function settings_form($settings)
	{
		// Check for Comment Column	and set checked radio button
		$checked_yes = '';
		$checked_no = '';
		
		if ($settings->comments  == "yes"){
			$checked_yes ="checked";
			$checked_no ="";
		}else{
			$checked_yes ="";
			$checked_no ="checked";
		};
		
		return form_open('', array('class' => 'dashForm')).'
			
			<p><label for="title">Widget Title:</label>
			<input type="text" name="title" value="'.$settings->title.'" /></p>
			
			<p><label for="channel_id">Channel ID:</label>
			<input type="text" name="channel_id" value="'.$settings->channel_id.'" /></p>
			
			<p><label for="limit">Entry Limit:</label>
			<input type="text" name="limit" value="'.$settings->limit.'" /></p>
			
			<p><label for="comments">Show Comment Total:</label>
			<input type="radio" name="comments" value="yes" '.$checked_yes.'>Yes
			<input type="radio" name="comments" value="no"'.$checked_no.'>No
			</p>
			
			<p><input type="submit" value="Save" /></p>
			
			'.form_close();
	}

}

/* End of file wgt.custom_entries.php */
/* Location: /system/expressionengine/third_party/dashee/widgets/wgt.custom_entries.php */