<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-globe">Map Info</span>
	</div>
	<div class="mws-panel-body">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th>Map ID</th>
					<th>Name</th>
					<th>Play Count</th>
					<th>Total Time Played</th>
					<th>Custom</th>
				</tr>
			</thead>
			<tbody>
				{maps}
					<tr>
						<td>{id}</td>
						<td>{name}</td>
						<td>{times}</td>
						<td>{time}</td>
						<td>{custom}</td>
					</tr>
				{/maps}
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".mws-datatable-fn").dataTable({sPaginationType: "full_numbers"});
});
</script>