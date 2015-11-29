

$(function() {

    var saved_boards = {
        'board1': ['', '7', '6', '', '1', '', '', '4', '3', '', '', '', '7', '', '2', '9', '', '', '', '9', '', '', '', '6', '', '', '', '', '', '', '', '6', '3', '2', '', '4', '4', '6', '', '', '', '', '', '1', '9', '1', '', '5', '4', '2', '', '', '', '', '', '', '', '2', '', '', '', '9', '', '', '', '4', '8', '', '7', '', '', '1', '9', '1', '', '', '5', '', '7', '2', ''],
        'board2': ['1', '', '', '', '3', '', '5', '9', '', '3', '', '', '5', '', '', '', '2', '', '', '5', '', '9', '', '2', '6', '3', '8', '4', '3', '', '', '', '', '', '', '', '', '', '', '6', '', '1', '', '', '', '', '', '', '', '', '', '', '8', '7', '6', '4', '7', '3', '', '8', '', '5', '', '', '1', '', '', '', '5', '', '', '9', '', '9', '2', '', '7', '', '', '', '3']
    };
    function Box(board) {
        var box = this;
        this.board = board;
        this.value = null;
        this.highlighttimeout = false;
        this.editing = false;

        this.setVal = function(val, initial) {
            this.value = val ? val : null;
            this.$editing_widget.val(this.value);
            $('>button', this.$elt).html(val ? val : '');

            if (val && initial)
                this.$elt.addClass('initial');
            else
                this.$elt.removeClass('initial');
        };
        this.highlight = function(delay) {
            this.$elt.addClass('highlight');

            var $elt = this.$elt;
            if (this.highlighttimeout)
                window.clearTimeout(this.highlighttimeout);
            this.highlighttimeout = window.setTimeout(function() {
                $elt.removeClass('highlight');
            }, delay);
        };

        this.makeeditorwidget = function() {
            var select = $('<select>');
            select.append('<option></option>');
            for (var i = 0; i < this.board.size; i++) {
                select.append('<option>' + (i + 1) + '</option>');
            }
            select.val(this.value);
            select.change(function() {

                box.setVal($(this).val(), true);

                box.$elt.removeClass('editing');
                box.editing = false;
                box.edit(false);

            });
            return select;
        };

        this.edit = function(close) {

            if (close) {
                this.$elt.removeClass('editing');
                this.editing = false;
            }
            else {
                if (this.editing)
                    return;
                this.$elt.addClass('editing');
                this.editing = true;
            }
        }

        this.$elt = $('<div class="box"><button class="btn btn-default "></div>');
        this.$editing_widget = this.makeeditorwidget();
        this.$elt.prepend(this.$editing_widget);
        this.$elt.click(function() {
            //if(box.editing = true);
            box.edit(box.editing);
        });
    }

    function Sudoku($elt) {
        this.boxes = [];
        this.size = 9;
        this.init = function(size, values) {
            this.boxes = [];
            this.size = size;
            $elt.html('');
            $elt.removeClass();
            $elt.addClass('size' + size);
            for (var i = 0; i < this.size; i++) {
                var line = $('<div>');
                line.addClass("row");
                for (var j = 0; j < this.size; j++) {
                    var box = new Box(this);
                    this.boxes.push(box);
                    line.append(box.$elt);
                }
                $elt.append(line);
            }

            this.load(values);
        };
        this.load = function(values) {
            if (values) {

                for (var pos in values) {
                    this.boxes[pos].setVal(values[pos], true);
                }
            }
            else
                for (var pos in this.boxes) {
                    this.boxes[pos].setVal('', false);
                }
        };
        this.getValues = function() {
            var values = [];
            $.each(this.boxes, function(i, box) {
                values.push(box.value);
            });
            return values;
        };
        this.resolve = function() {
            $elt.find('div.alert').remove();
            var values = this.getValues();
            var self = this;
            $.post('ajax.php', {values: values, matrix_size: self.size}, function(response) {
                self.displayResolution(response.log, true);
                /*for (var pos in response.result) {
                 self.boxes[pos].setVal(response.result[pos]);
                 }*/
            }).fail(function(jqXHR, textStatus, errorThrown) {
                var message = jqXHR.responseJSON.message;
                $elt.prepend('\
<div class="alert alert-warning alert-dismissible" role="alert">\
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
    <strong>' + message + '</strong>.\
</div>');
                window.setTimeout(function() {
                    $elt.find('div.alert').remove();
                }, 5000);

                self.displayResolution(jqXHR.responseJSON.log);
                $('#resolve').attr('disabled', false);
            });
        };

        ;
        this.displayResolution = function(log_history, full) {

            var log_line = 0;
            var self = this;
            var log_interval = window.setInterval(function() {
                if (log_history[log_line]) {
                    var what = log_history[log_line].what;
                    var position = log_history[log_line].position;
                    var value = log_history[log_line].value;
                    var tohighlight = [];

                    if (what == 'box') {
                        self.boxes[position].setVal(value);
                        tohighlight.push(position);
                    }
                    if (what == 'line') {
                        for (var i = 0; i < self.size; i++) {
                            tohighlight.push(position + i);
                        }
                    }
                    if (what == 'column') {
                        for (var i = 0; i < self.size; i++) {
                            tohighlight.push(position + i * self.size);
                        }
                    }
                    if (what == 'square') {

                        for (var i = 0; i < Math.sqrt(self.size); i++) {
                            for (var j = 0; j < Math.sqrt(self.size); j++) {
                                tohighlight.push(position + j + i * self.size);
                            }
                        }
                    }
                    for (var i in tohighlight) {
                        self.boxes[tohighlight[i]].highlight(what == 'box' ? 200 : 600);
                    }
                    log_line++;
                }
                else {

                    window.clearInterval(log_interval);
                    if (full) {
                        for (i in self.boxes) {
                            self.boxes[i].highlight(1000);
                        }
                    }
                    $('#resolve').attr('disabled', false);
                }
            }, 100);
        };
        this.init(9);
    }
    ;
    var grid = new Sudoku($('#grid'));
    $('#matrix_size').change(function() {
        grid.init($(this).val(), null);
    });
    $('#board').change(function() {
        if (saved_boards[$(this).val()]) {
            var size = Math.sqrt(saved_boards[$(this).val()].length);
            $('#matrix_size').val(size);
            grid.init(size, saved_boards[$(this).val()]);
        }
        else
            grid.init($('#matrix_size').val());
        $('#resolve').attr('disabled', false);
    });
    $('#resolve').click(function() {
        grid.resolve();
        $(this).attr('disabled', true);
    });
});