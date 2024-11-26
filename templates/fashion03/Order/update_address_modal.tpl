<div id="update-address-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
			<div class="modal-header p-4">
				<div class="h4 font-weight-bold text-uppercase mb-0">
					{__d('template', 'cap_nhat_dia_chi')}
				</div>
				<button type="button" class="close icon-close" data-dismiss="modal" aria-label="Close">
					<i class="fa-light fa-xmark"></i>
				</button>
			</div>
			<div class="modal-body p-4">
				<ul class="nav box-update-address" role="tablist">
				  	<li class="nav-item">
				    	<a class="nav-link active color-black" data-toggle="tab" href="#tab-delivery-address" role="tab" aria-controls="tab-delivery-address" aria-selected="true">
				    		{__d('template', 'dia_chi_nhan_hang')}
				    	</a>
				  	</li>
				  	<li class="nav-item">
				    	<a class="nav-link color-black" data-toggle="tab" href="#tab-member-address" role="tab" aria-controls="tab-member-address" aria-selected="true">
				    		{__d('template', 'them_dia_chi')}
				    	</a>
				  	</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="tab-delivery-address" role="tabpanel">
				  		{$this->element('../Order/element_delivery_address')}
				  	</div>

				  	<div class="tab-pane fade" id="tab-member-address" role="tabpanel">
				  		{$this->element('../Member/element_address_form')}
				  	</div>
				</div>
			</div>
        </div>   
    </div>
</div>