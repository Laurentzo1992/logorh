(function( $ ) {
	$.widget( "custom.combobox", {
		_create : function() {
			this.wrapper = $("<span>").addClass(
					"custom-combobox")
					.insertAfter(this.element);
			this.element.hide();
			this._createAutocomplete();
			this._createHiddenField();
			this._createShowAllButton();
		},
		_createAutocomplete : function() {
			var selected = this.element.children(":selected"), value = selected
					.val() ? selected.text() : "";
			this.defaultV = selected.val() ? $(selected).attr(
					'value') : "";

			this.inputName = this.element.attr('name');
			this.element.attr('name', '');
			this.input = $("<input>").appendTo(this.wrapper)
					.val(value).attr("title", "").addClass(
							"custom-combobox-input")
					.autocomplete( {
						delay : 0,
						minLength : 0,
						source : $.proxy(this, "_source")
					}).tooltip( {
						tooltipClass : "ui-state-highlight"
					});
			this._on(this.input, {
				autocompleteselect : function(event, ui) {
					this._updateHiddenField($(ui.item.option)
							.attr('value'));
					ui.item.option.selected = true;
					this._trigger("select", event, {
						item : ui.item.option
					});
				},
				autocompletechange : "_removeIfInvalid"
			});
		},
		_createShowAllButton : function() {
			var input = this.input, wasOpen = false;
			$("<a>").attr("tabIndex", -1).attr("title",
					"Afficher tous").tooltip().hide().appendTo(
					this.wrapper).button( {
				icons : {
					primary : "ui-icon-triangle-1-s"
				},
				text : false
			}).removeClass("ui-corner-all").addClass(
					"custom-combobox-toggle").mousedown(
					function() {
						wasOpen = input.autocomplete("widget")
								.is(":visible");
					}).click(function() {
				input.focus();
				// Close if already visible
				if (wasOpen) {
					return;
				}
				// Pass empty string as value to search for,
				// displaying all results
				input.autocomplete("search", "");
			});
		},
		_source : function(request, response) {
			var currentClass = this;
			var matcher = new RegExp($.ui.autocomplete
					.escapeRegex(request.term), "i");
			response(this.element.children("option").map(
					function() {
						var text = $(this).text();
						if (this.value
								&& (!request.term || matcher
										.test(text))) {
							currentClass._updateHiddenField($(
									this).attr('value'));
							return {
								label : text,
								value : text,
								option : this
							};
						} // endif
					}));
		},
		_createHiddenField : function() {
			$("<input>").appendTo(this.wrapper).attr("type",
					"hidden").attr("title", "").attr("name",
					this.inputName)
					.attr("value", this.defaultV)
		},
		_updateHiddenField : function(value) {
			$('input[name="' + this.inputName + '"]')
					.val(value);
		},
		_removeIfInvalid : function(event, ui) {
			// Selected an item, nothing to do
			if (ui.item) {
				return;
			}
			// Search for a match (case-insensitive)
			var value = this.input.val(), valueLowerCase = value
					.toLowerCase(), valid = false;
			this.element
					.children("option")
					.each(
							function() {
								if ($(this).text()
										.toLowerCase() === valueLowerCase) {
									this.selected = valid = true;
									return false;
								}
							});
			// Found a match, nothing to do
			if (valid) {
				return;
			}
			// Remove invalid value

			this._updateHiddenField('0');
			$('#' + this.element.attr('id') + 'CreateForm')
					.slideDown('fast');
			var value = this.input.val();
			$('#' + this.element.attr('id') + 'InsertValue')
					.val(value);
			this.input
					.val("")
					.attr(
							"title",
							value
									+ " n'a pas ete trouvee dans la liste")
					.tooltip("open");
			this.element.val("");
			this._delay(function() {
				this.input.tooltip("close").attr("title", "");
			}, 2500);
			this.input.data("ui-autocomplete").term = "";
		},
		_destroy : function() {
			this.wrapper.remove();
			this.element.show();
		}
	});
})(jQuery);
$(function() {
	$("select.combobox").combobox();
});