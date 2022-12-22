'use strict';

function addToBusiness(e, vat) {
	var id = parseInt($(e).closest('tr').attr('title'));
	console.log([id, vat]);
	$.post('./callback.php', {
		a: 'add_to_business',
		id: id,
		vat: (vat ? 1 : 0),
		});
}

function removeFromBusiness(e) {
	var id = parseInt($(e).closest('tr').attr('title'));
	console.log(id);
	$.post('./callback.php', {
		a: 'remove_from_business',
		id: id,
		});
}
