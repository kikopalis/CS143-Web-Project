$(document).ready(function () {

    //------------------------------------POS---------------------------------//
    //add event on nav-items that will change css when they are click
    $('.nav-item').on('click', function () {
        $('.active').removeClass('active')
        $(this).addClass('active')
        $('div[class*="nav-item-content justify-content-center d-flex"]').toggleClass('d-none d-flex')
        $('.nav-item-content').css({
            'opacity': 0
        })
        $('.nav-item-content').eq($(this).index()).toggleClass('d-none d-flex')
        $('.nav-item-content').eq($(this).index()).animate({
            'opacity': 1
        }, 100)
        //refresh product list to reflect the result inside the product lists
        if ($(this).index() == 0)
            $('#form-search1').submit();
        if ($(this).index() == 2)
            $('#form-search2').submit();
    })

    $('#form-search1 input').on('input', function () {
        $('#form-search1').submit();
    })

    $(this).on('click', '#product-list1 tr', function () {
        var pid = $(this).children().next().html()
        var index = $('#invoice-list').children().length;
        $.each($('#invoice-list').children(), function () {
            if ($(this).children().first().html() == pid)
                index = $(this).index()
        })
        $.ajax({
            type: "GET",
            url: "cart.php",
            data: {
                'pid': pid,
                'tocart': '++'
            },
            dataType: "html",
            success: function (response) {
                if (response) {
                    if (index > $('#invoice-list').html(response).children().length) {
                        index -= 1;
                        $('#invoice-list').children().eq(index).css('opacity', 0)
                        $('#invoice-list').children().eq(index).animate({
                            'opacity': 1
                        }, 250)
                    } else {
                        $('#invoice-list').children().eq(index).css('opacity', 0)
                        $('#invoice-list').children().eq(index).animate({
                            'opacity': 1
                        }, 250)
                    }
                    //refresh product list to reflect the result
                    $('#form-search1').submit();
                    $('#cash').trigger('focusout');
                    $('#form-search2').submit();
                    checkNotification();
                }
            }
        });
    })

    $(this).on('focusout', '#invoice-list input', function () {
        var pid = $(this).parent().parent().children().first().html();
        var tocart = $(this).val();
        var max = $(this).attr('max');
        var min = $(this).attr('min');

        if (max - tocart <= 0) {
            $(this).val(max);
            tocart = max;
        }
        if (tocart <= min) {
            $(this).val(min);
            tocart = min;
        }

        $.ajax({
            type: "get",
            url: "cart.php",
            data: {
                'pid': pid,
                'tocart': tocart
            },
            dataType: "html",
            success: function (response) {
                if (response) {
                    $('#invoice-list').html(response);
                    //refresh product list to reflect the result
                    $('#form-search2').submit()
                    $('#form-search1').submit();
                    $('#cash').trigger('focusout');
                }
            }
        });
    })

    $(this).on('click', '#invoice-list a', function (e) {
        e.preventDefault();
        var index = $(this).parent().parent().index();
        var pid = $(this).parent().parent().children().first().html();
        $(this).parent().parent().animate({
            'opacity': 0
        }, 500)
        $.ajax({
            type: "get",
            url: "cart.php",
            data: {
                'pid': pid,
                'tocart': '0'
            },
            dataType: "html",
            success: function (response) {
                $('#invoice-list').html(response).children().eq(index).css('opacity', 0)
                $('#invoice-list').children().eq(index).animate({
                    'opacity': 1
                }, 250)
                $('#cash').trigger('focusout');
                $('#form-search1').submit();
            }
        });
    })

    $('#invoice-submit').on('click', function (e) {
        e.preventDefault();
        var cash = parseFloat($('#cash').val());
        $.ajax({
            type: "post",
            url: "invoice.php",
            data: {
                'invoice_submit': 'ok',
                'cash': cash
            },
            dataType: "html",
            success: function (response) {
                $('#invoice-show-error').html('');
                if (response.match('<span class="text-success">Success!</span>')) {
                    $('#invoice-info').html('');
                }
                get_cart();
                $('#cash').val('');
                $('#invoice-show-error').html(response).css({
                    'opacity': 0
                });
                $('#invoice-show-error').animate({
                    'opacity': 1
                }, 250);
                get_reports();
                get_logs();
            }
        });
    });

    $('#cash').on('focusout', function () {
        var cash = $(this).val();
        $.ajax({
            type: "POST",
            url: "invoice.php",
            data: {
                'get_invoice': 'ok',
                'cash': cash
            },
            dataType: "html",
            success: function (response) {
                $('#invoice-show-error').html('');
                $('#invoice-info').html(response);
            }
        });
    });

    $("#invoice input").on('keyup', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $(this).trigger('focusout');
        }
    });

    $(this).on('keyup', '#invoice-list input', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $(this).trigger('focusout');
        }
    });

    $('#cash').trigger('focusout');
    get_cart();
    //------------------------------------PRODUCTS---------------------------------//
    //focus on input when "add" modal is shown
    $('#add-form').on('shown.bs.modal', function () {
        $('#pid').focus();
    })
    //remove value in file input when click. this prevents the value to be retained when we click cancel during selection of image
    $('#imagefile').on('click', function () {
        $(this).next().html('Choose a file (2MB limit)');
        $(this).val('');
        $('#imagefile').prop('required', true);
    })
    //sets the value inside file input to the file that user selects
    $('#imagefile').on('change', function () {
        $(this).next().html($(this)[0].files[0].name)
    })

    $(this).on('click', '#product-list2 tr', function () {
        var pid = $(this).children().next().html()
        $.ajax({
            type: "post",
            url: "products.php",
            data: {
                'pid': pid,
                'edit': 'ok'
            },
            dataType: "json",
            success: function (response) {
                $('#add-form-title').html('Edit Product')
                var inputs = $('#add-form input');
                var index = 0;
                for (element in response) {
                    if (index == 4) {
                        $('#add-form label').html(response[element]);
                    } else
                        inputs.eq(index).val(response[element]);
                    index++;
                }
                $('#imagefile').prop('required', false);
                $('#add-form-delete').addClass('d-block');
                $('#add-form-delete').removeClass('d-none');
                $('#add-form').modal('show');
            }
        });
    })

    //send "add data form" to server and display result inside the modal for adding product
    $('#add-form').on('submit', function (e) {
        e.preventDefault()
        var formdata = new FormData($('#add-form')[0])
        var file = $('#imagefile')[0].files[0]
        formdata.append('imagefile', file)
        if ($('#add-form-title').html() == 'Add Product') {
            formdata.append('add', 'ok')
        }
        
        $.ajax({
            method: 'POST',
            url: 'products.php',
            processData: false,
            contentType: false,
            data: formdata,
            dataType: "html",
            success: function (response) {
                $('#show-error').css('opacity', 0)
                if (response.match('<span class="text-success">Successfully added!</span>') ||
                    response.match('<span class="text-success">Successfully updated!</span>')) {
                    let pname = $('#name').val()
                    $('#add-form-clear').click()
                    $('#show-error').html(pname + '<br>')
                } else
                    $('#show-error').html('')
                $('#show-error').append(response).animate({
                    'opacity': 1
                }, 250)
                //refresh product list to reflect inside the product lists
                $('#form-search2').submit()
                checkNotification();
                get_logs(); 
            }
        })
    })

    //clear inputs on every click in add form modal
    $('#add-form-clear').on('click', function () {
        $('#show-error').html('')
        $('#imagefile').next().html("Choose a file (2MB limit)")
    })

    $('#add-form').on('hidden.bs.modal', function () {
        $('#add-form-clear').click();
        $('#add-form-title').html('Add Product')
        $('#imagefile').prop('required', true);
        $('#add-form-delete').addClass('d-none');
        $('#add-form-delete').removeClass('d-block');
    })

    $('#form-search2 input').on('input', function () {
        $('#form-search2').submit();
        checkNotification();
    })

    $('#add-form-delete').on('click', function (e) {
        e.preventDefault()
        $.ajax({
            type: "post",
            url: "products.php",
            data: {
                'remove': 'ok'
            },
            dataType: "html",
            success: function (response) {
                $('#show-error').css('opacity', 0)
                if (response.match('<span class="text-success">Successfully removed!</span>')) {
                    let pname = $('#name').val()
                    $('#add-form-clear').click()
                    $('#show-error').html(pname + '<br>')
                } else
                    $('#show-error').html('')
                $('#show-error').append(response).animate({
                    'opacity': 1
                }, 250)
                //refresh product list after adding to reflect the added product inside the product lists
                $('#form-search2').submit()
                checkNotification();
                get_logs(); 
            }
        });
    })

    $('#form-search2').on('submit', function (e) {
        e.preventDefault()
        $.ajax({
            type: "POST",
            url: "products.php",
            data: {
                'product-search2': $('#product-search2').val(),
                'product-search2-submit': 'ok'
            },
            dataType: "html",
            success: function (response) {
                $('#product-list2').html(response);
                checkNotification();
            }
        });
    })
    $('#form-search1').on('submit', function (e) {
        e.preventDefault()
        $.ajax({
            type: "POST",
            url: "products.php",
            data: {
                'product-search1': $('#product-search1').val(),
                'product-search1-submit': 'ok'
            },
            dataType: "html",
            success: function (response) {
                $('#product-list1').html(response);
                $('#form-search2').submit();
                checkNotification();
            }
        });
    })

    $('#clear-logs').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "logs.php",
            data: {"clear" : "ok"},
            dataType: "html",
            success: function (response) {
                $('#logs-container').html(response);
            }
        });
    })
    $('#clear-logs2').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "logs.php",
            data: {"clear" : "acivity"},
            dataType: "html",
            success: function (response) {
                $('#logs-container2').html(response);
            }
        });
    })

    $(this).on('click', '#reports-container tr', function () {
        var invoice_id = $(this).children().next().html();
        var Total = parseFloat($(this).children().next().next().html());
        var Change = parseFloat($(this).children().next().next().next().html());
        $('#invoice_content_id').html("Invoice ID: " + invoice_id);
        $('#invoice_content_info span').eq(0).html("Total: " + Total.toFixed(2));
        $('#invoice_content_info span').eq(1).html("Cash: " + (Total+Change).toFixed(2));
        $('#invoice_content_info span').eq(2).html("Change: " + Change.toFixed(2));
        $.ajax({
            type: "GET",
            url: "reports.php",
            data: {"get_invoice_content" : invoice_id},
            dataType: "html",
            success: function (response) {
                $('#invoice_content_list').html(response);
                $('#invoice_content').modal('show');
            }
        });
    })

    $('#product-search2').submit()
    $('#product-search1').submit()
    get_logs();  
    get_reports();
})

function checkNotification(){
    var tr = $('#product-list2 tr');
    var outOfStock = "";
    var lowStock = "";
    for (var x = 0; x < tr.length; x++) {
        if (tr.eq(x).children().eq(4).html().indexOf("Out of") != -1)
            outOfStock++;
        if (tr.eq(x).children().eq(4).html() < 5)
            lowStock++;

    }
    $('#products-notification1').html(lowStock);
    $('#products-notification2').html(outOfStock);
}

//function to hide nav-item based on user id
//used as a function because the parameter/argument uid can only be accessed on .php
function hideNavBarItem(uid) {
    $(document).ready(function () {
        if (uid == "2") {
            $(".nav-item").eq(0).css("display", "none")
            $(".nav-item").eq(1).trigger('click')
        }
        if (uid == "3") {
            $(".nav-item").eq(1).css("display", "none")
            $(".nav-item").eq(2).css("display", "none")
        }
        $('.nav-item-content').animate({
            'opacity': 1
        }, 100)
    })
}


function get_cart() {
    $.ajax({
        type: "GET",
        url: "cart.php",
        data: {
            'getcart': 'ok'
        },
        dataType: "html",
        success: function (response) {
            $('#invoice-list').html(response);
        }
    });
}

function get_logs() {
    $.ajax({
        type: "GET",
        url: "logs.php",
        data: {"get" : "ok"},
        dataType: "html",
        success: function (response) {
            $('#logs-container').html(response);
        }
    });
    $.ajax({
        type: "GET",
        url: "logs.php",
        data: {"get" : "activity"},
        dataType: "html",
        success: function (response) {
            $('#logs-container2').html(response);
        }
    });
}

function get_reports() {
    $.ajax({
        type: "GET",
        url: "reports.php",
        data: {"get_invoice_list" : "ok"},
        dataType: "html",
        success: function (response) {
            $('#reports-container').html(response);
        }
    });
}