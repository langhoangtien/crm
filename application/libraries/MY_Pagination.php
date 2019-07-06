<?php
class MY_Pagination extends CI_Pagination 
{
	var $cover_page_open		= '';
	var $cover_page_close		= '';
	var $display_info_page		= false;
	var $route					= '';
	var $routePage				= '';
	var $classTag				= '';
	var $options				= array();
	var $original_link			= '';

	public function __construct()
	{
		parent::__construct(); 
	}
	
	function createConfig($type = null, $suffix = null) {
		if($type == 'admin') {
			$this->first_link = 'Start';
			$this->first_tag_open = '<div class="button2-right"><div class="start">';
			$this->first_tag_close = '</div></div>';
			
			$this->prev_link = 'Prev';
			$this->prev_tag_open = '<div class="button2-right"><div class="prev">';
			$this->prev_tag_close = '</div></div>';
			
			$this->next_link = 'Next';
			$this->next_tag_open = '<div class="button2-left"><div class="next">';
			$this->next_tag_close = '</div></div>';
			
			$this->last_link = 'End';
			$this->last_tag_open = '<div class="button2-left"><div class="end">';
			$this->last_tag_close = '</div></div>';
			
			$this->cover_page_open = '<div class="button2-left"><div class="page">';
			$this->cover_page_close = '</div></div>';
			
			$this->cur_tag_open = '<span>';
			$this->cur_tag_close = '</span>';
			
			$this->display_info_page = true;
		}elseif($type == 'front-end') {

			$this->first_link = FALSE;
			$this->last_link   = FALSE;
	
			$this->prev_link = '<';
			$this->prev_tag_open = '';
			$this->prev_tag_close = '';
				
			$this->next_link = '>';
			$this->next_tag_open = '';
			$this->next_tag_close = '';
				
			$this->cover_page_open = '';
			$this->cover_page_close = '';
				
			$this->cur_tag_open = '<strong>';
			$this->cur_tag_close = '</strong>';
			
			$this->full_tag_open = '<div class="text-center"><div class="pagination hidden-print alternate text-center" id="pagination_bottom">';
			$this->full_tag_close = '</div></div>';
				
			$this->display_info_page = false;
		}
	}
	

	
	function create_ajax()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}
		
		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);
		
		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}
		
		// Set the base page index for starting page number
		if ($this->use_page_numbers)
		{
			$base_page = 1;
		}
		else
		{
			$base_page = 0;
		}
		
		// Determine the current page number.
		$CI =& get_instance();
		
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != $base_page)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);
		
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != $base_page)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);
		
				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		// Set current page to 1 if using page numbers instead of offset
		if ($this->use_page_numbers AND $this->cur_page == 0)
		{
			$this->cur_page = $base_page;
		}
		
		$this->num_links = (int)$this->num_links;
		
		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
		
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = $base_page;
		}
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		else
		{
			if ($this->cur_page > $this->total_rows)
			{
				$this->cur_page = ($num_pages - 1) * $this->per_page;
			}
		}
		
		$uri_page_number = $this->cur_page;
		
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
		}
		
		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
		
		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}
		
		// And here we go...
		$output = '';
		
		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
			$output .= $this->first_tag_open.'<a href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}
		
		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
		{
			if ($this->use_page_numbers)
			{
				$i = $uri_page_number - 1;
			}
			else
			{
				$i = $uri_page_number - $this->per_page;
			}
		
			if ($i == 0 && $this->first_url != '')
			{
				
				$output .= $this->prev_tag_open.'<a href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				$result['prev'] = 1;
			}
			else
			{

				$i = ($i == 0) ? 1 : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			
				$result['prev'] = $i;
			}
			
	
		}
		
		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			$output .= $this->cover_page_open;
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				if ($this->use_page_numbers)
				{
					$i = $loop;
				}
				else
				{
					$i = ($loop * $this->per_page) - $this->per_page;
				}
		
				if ($i >= $base_page)
				{
					if ($this->cur_page == $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
						$result['current'] = $loop;
					}
					else
					{
						$n = ($i == $base_page) ? '' : $i;
				
						if ($n == '' && $this->first_url != '')
						{
							$output .= $this->num_tag_open.'<a href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
							
							$result['page-1'] = 1;
						}
						else
						{
							$n = ($n == '') ? $this->suffix : $this->prefix.$n.$this->suffix;
				
							if(empty($n))
								$n = 1;
							$output .= $this->num_tag_open.'<a href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
							
							$result['page-'.$n] = $n;
						}
		
					}
				}
		   }
		   $output .= $this->cover_page_close;
	 	}
	 	// Render the "next" link
	 	if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
	 	{
	 		if ($this->use_page_numbers)
	 		{
	 			$i = $this->cur_page + 1;
	 		}
	 		else
	 		{
	 			$i = ($this->cur_page * $this->per_page);
	 		}
	 	
	 		$output .= $this->next_tag_open.'<a href="javascript:;" data-page="'.$i.'">'.$this->next_link.'</a>'.$this->next_tag_close;
	 		
	 		$result['next'] = $i;
	 	}
	 	
	 	// Render the "Last" link
	 	if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
	 	{
	 		if ($this->use_page_numbers)
	 		{
	 			$i = $num_pages;
	 		}
	 		else
	 		{
	 			$i = (($num_pages * $this->per_page) - $this->per_page);
	 		}
	 		$output .= $this->last_tag_open.'<a href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
	 	}
	 	
	 	if($this->display_info_page == true) {
	 		$output .= '<div class="limit">Page '.$this->cur_page.' Of '.$num_pages.' - Total: <b>'.$this->total_rows.'</b></div>';
	 	}
	 	
	 	// Kill double slashes.  Note: Sometimes we can end up with a double slash
	 	// in the penultimate link so we'll kill all double slashes.
	 	$output = preg_replace("#([^:])//+#", "\\1/", $output);
	 	
	 	// Add the wrapper HTML if exists
	 	$output = $this->full_tag_open.$output.$this->full_tag_close;
	 	
	 	return $result;
	 	
	}

	
	
	
	
	/* 
	* MY PAGINATION CREATE LINKS
	* 
	* This is an other vesion of create links
	* You can use the following variable to create pagination yourself with your view
	* You have to config use_page_numbers = TRUE
	*
	* Check if has more page
	* @var (bool) pagionator['hasMorePage']
	*
	* Next page, previous page button URL
	* @var (string) pagionator['nextPageUrl']
	* @var (string) pagionator['previousPageUrl']
	*
	* Check is on first page
	* @var (bool) pagionator['onFirstPage']
	*
	* Check is button move to the first page enable
	* @var (bool) pagionator['firstLink']
	* @var (string) pagionator['firstPageUrl']
	*
	* Check is button move to the last page enable
	* @var (bool) pagionator['lastLink']
	* @var (string) pagionator['lastPageUrl']
	* 
	* Element between these button above
	*
	* elements(
	* 'page' => pageNumber,
	* 'url' => urlOfPage
	*)
	*/
	function my_Create_Links()
	{
		$data = array();
		$elements = array();
		$j = 1;
		// If our item count or per-page total is zero there is no need to continue.
		// Note: DO NOT change the operator to === here!
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages === 1)
		{
			return '';
		}

		// Check the user defined number of links.
		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 0)
		{
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items.
		// Note: Has nothing to do with any other query string option.
		if ($this->reuse_query_string === TRUE)
		{
			$get = $this->CI->input->get();

			// Unset the controll, method, old-school routing options
			unset($get['c'], $get['m'], $get[$this->query_string_segment]);
		}
		else
		{
			$get = array();
		}

		// Put together our base and first URLs.
		// Note: DO NOT append to the properties as that would break successive calls
		$base_url = trim($this->base_url);
		$first_url = $this->first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			if ($first_url === '')
			{
				$first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended.
				if ( ! empty($get))
				{
					$first_url .= $query_string_sep.http_build_query($get);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			$base_url .= $query_string_sep.http_build_query(array_merge($get, array($this->query_string_segment => '')));
		}
		else
		{
			// Standard segment mode.
			// Generate our saved query string to append later after the page number.
			if ( ! empty($get))
			{
				$query_string = $query_string_sep.http_build_query($get);
				$this->suffix .= $query_string;
			}

			// Does the base_url have the query string in it?
			// If we're supposed to save it, remove it so we can append it later.
			if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE)
			{
				$base_url = substr($base_url, 0, $base_query_pos);
			}

			if ($first_url === '')
			{
				$first_url = $base_url.$query_string;
			}

			$base_url = rtrim($base_url, '/').'/';
		}

		// Determine the current page number.
		$base_page = ($this->use_page_numbers) ? 1 : 0;

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			$this->cur_page = $this->CI->input->get($this->query_string_segment);
		}
		else
		{
			// Default to the last segment number if one hasn't been defined.
			if ($this->uri_segment === 0)
			{
				$this->uri_segment = count($this->CI->uri->segment_array());
			}

			$this->cur_page = $this->CI->uri->segment($this->uri_segment);

			// Remove any specified prefix/suffix from the segment.
			if ($this->prefix !== '' OR $this->suffix !== '')
			{
				$this->cur_page = str_replace(array($this->prefix, $this->suffix), '', $this->cur_page);
			}
		}

		// If something isn't quite right, back to the default base page.
		if ( ! ctype_digit($this->cur_page) OR ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			// Make sure we're using integers for comparisons later.
			$this->cur_page = (int) $this->cur_page;
		}

		// Is the page number beyond the result range?
		// If so, we show the last page.
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;

		// If we're using offset instead of page numbers, convert it
		// to a page number, so we can generate the surrounding number links.
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page/$this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		$start	= (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end	= (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
		$data['currentPage'] = $this->cur_page;
		// And here we go...
		$output = '';

		// Render the "First" link.
		if ($this->first_link !== FALSE && $this->cur_page > ($this->num_links + 1 + ! $this->num_links))
		{
			$data['firstPageUrl'] = $first_url.'/1';
			$data['firstLink'] = TRUE;
		}
		else
		{
			$data['firstLink'] = FALSE;
		}

		// Render the "Previous" link.
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this->per_page;
			$data['onFirstPage'] = FALSE;
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, ($this->cur_page - 1));

			if ($i === $base_page)
			{
				// First page
				$data['previousPageUrl'] = $first_url.'/1';
			}
			else
			{
				$append = $this->prefix.$i.$this->suffix;
				$data['previousPageUrl'] = $base_url.$append;
			}

		}
		else
		{
			$data['onFirstPage'] = TRUE;
		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links
			for ($loop = $start - 1; $loop <= $end; $loop++)
			{
				$i = ($this->use_page_numbers) ? $loop : ($loop * $this->per_page) - $this->per_page;

				if ($i >= $base_page)
				{
					$elements[$j] = array(
						'page' => $i,
						'url' => $base_url.$this->prefix.$i.$this->suffix
						);
					$j++;
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;

			$data['nextPageUrl'] = $base_url.$this->prefix.$i.$this->suffix;
			$data['hasMorePage'] = TRUE;
		}
		else
		{
			$data['hasMorePage'] = FALSE;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + ! $this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;

			$data['lastPageUrl'] = $base_url.$this->prefix.$i.$this->suffix;
			$data['lastLink'] = TRUE;
		}
		else
		{
			$data['lastLink'] = FALSE;
		}
		
		return array(
			'paginator' => $data,
			'elements' => $elements
			);
	}
}