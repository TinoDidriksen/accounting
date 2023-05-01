'use strict';

function addToBusiness(e, vat) {
	let id = parseInt($(e).closest('tr').attr('title'));
	console.log([id, vat]);
	$.post('./callback.php', {
		a: 'add_to_business',
		id: id,
		vat: (vat ? 1 : 0),
		});
}

function removeFromBusiness(e) {
	let id = parseInt($(e).closest('tr').attr('title'));
	console.log(id);
	$.post('./callback.php', {
		a: 'remove_from_business',
		id: id,
		});
}

function changeEntryClass() {
	let id = parseInt($(this).closest('tr').attr('title'));
	let val = parseInt($(this).val());
	$.post('./callback.php', {
		a: 'entry_class_change',
		id: id,
		val: val,
		});
}

$(window).on('load', function() {
	$('.eclass').change(changeEntryClass);
});
