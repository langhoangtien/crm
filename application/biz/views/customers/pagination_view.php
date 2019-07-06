<ul class = "pagination">
	<?php if($pagination !== ''):?>
	<!-- First Page Link-->
	<?php if($pagination['paginator']['firstLink']):?>
	<li><a class ="linkClicked" href ="<?php echo $pagination['paginator']['firstPageUrl']?>"><?php echo lang('common_pagination_firstPage');?></a></li>
	<?php endif;?>
	
	<!-- Previous Page Link-->
	<?php if($pagination['paginator']['onFirstPage']):?>
	<li><span><?php echo lang('common_pagination_prePage');?></span></li>
	<?php else:?>
	<li><a class ="linkClicked" href ="<?php echo $pagination['paginator']['previousPageUrl']?>"><?php echo lang('common_pagination_prePage');?></a></li>
	<?php endif;?>
	
	<!-- Pagination elements -->
	<?php foreach($pagination['elements'] as $element):?>
	
	<?php if($pagination['paginator']['currentPage'] == $element['page']):?>
	<li class ="active"><span><?php echo $element['page'];?></span></li>
	<?php else:?>
	<li><a class ="linkClicked" href ="<?php echo $element['url']?>"><?php echo $element['page'];?></a></li>  
	<?php endif;?>
	<?php endforeach;?>
	
	<!-- Next Page Link-->
	<?php if(!$pagination['paginator']['hasMorePage']):?>
	<li><span><?php echo lang('common_pagination_nextPage');?></span></li>
	<?php else:?>
	<li><a class ="linkClicked" href ="<?php echo $pagination['paginator']['nextPageUrl']?>"><?php echo lang('common_pagination_nextPage');?></a></li>
	<?php endif;?>
	
	<!-- Last Page Link-->
	<?php if($pagination['paginator']['lastLink']):?>
	<li><a class ="linkClicked" href ="<?php echo $pagination['paginator']['lastPageUrl']?>"><?php echo lang('common_pagination_lastPage');?></a></li>
	<?php endif;?>
	<?php endif;?>
</ul>