<div id="modal-instruct" class="modal">
  	<div class="header-modal">
		<div class="title-modal">
			{__d('filemanager', 'luu_y')}
		</div>
	</div>
	
	<div class="info">
		<div class="item-info-note">
			<div class="title">
				{__d('filemanager', 'dung_luong')}
			</div>
			<p>
				{__d('filemanager', 'anh_tai_len_gioi_han_khong_duoc_qua')}: 
				<span class="font-weight-bold capacity red">
					{$this->UtilitiesAdmin->parseFileSize(MAX_IMAGE_SIZE, 0)}
				</span>
			</p>
			<p>
				{__d('filemanager', 'tep_tai_len_tru_dinh_dang_anh_gioi_han_khong_duoc_qua')}: 
				<span class="font-weight-bold capacity red">
					{$this->UtilitiesAdmin->parseFileSize(MAX_FILE_SIZE, 0)}
				</span>
			</p>
		</div>
		
		<div class="item-info-note">
			<div class="title">
				{__d('filemanager', 'dinh_dang_tep_tin_tai_len')}
			</div>
			<p>
				<span class="font-weight-bold">
					{__d('filemanager', 'duoi_image')}:
				</span>	
				<span class="red">
					png, jpg, jpeg, ico, svg, webp, gif
				</span>
			</p>

			<p>
				<span class="font-weight-bold">
					{__d('filemanager', 'duoi_file')}:
				</span>	
				<span class="red">
					doc, docx, xls, xlsx, csv, ppt, pptx, pdf, txt 
				</span>
			</p>

			<p>
				<span class="font-weight-bold">
					{__d('filemanager', 'duoi_video')}:
				</span>	
				<span class="red">
					mp4, avi, mpeg
				</span>
			</p>

			<p>
				<span class="font-weight-bold">
					{__d('filemanager', 'duoi_am_thanh')}:
				</span>	
				<span class="red">
					mp3, wav, aac
				</span>
			</p>
			
			<p>
				<span class="font-weight-bold">
					{__d('filemanager', 'duoi_file_nen')}:
				</span>	
				<span class="red">
					zip, rar, 7z
				</span>
			</p>
		</div>
		<div class="item-info-note">
			<div class="title">
				{__d('filemanager', 'phim_tat')}
			</div>
			<table>
			    <tbody>
			        <tr>
			        	<th>
			        		{__d('filemanager', 'nhan_to_hop_phim')}
			        	</th>
					    <th>
					    	{__d('filemanager', 'de_thuc_hien_thao_tac')}
					    </th>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + A
			            </td>
			            <td>
			            	{__d('filemanager', 'chon_tat_ca_tep_trong_thu_muc')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + D
			            </td>
			            <td>
			            	{__d('filemanager', 'tai_file_ve_may')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + C
			            </td>
			            <td>
			            	{__d('filemanager', 'sao_chep_tep')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + X
			            </td>
			            <td>
			            	{__d('filemanager', 'cat_tep')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + V
			            </td>
			            <td>
			            	{__d('filemanager', 'dan_tep')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + R
			            </td>
			            <td>
			            	{__d('filemanager', 'doi_ten_tep')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Ctrl + L
			            </td>
			            <td>
			            	{__d('filemanager', 'duong_dan_tep')}
			            </td>
			        </tr>
			        <tr>
			            <td>
			            	Delete {__d('filemanager', 'hoac')} Backspace
			            </td>
			            <td>
			            	{__d('filemanager', 'xoa_tep')}
			            </td>
			        </tr>
			    </tbody>
			</table>
		</div>
	</div>
</div>