jQuery(document).ready(function($) {
    //Datepicker
    $( ".bb-filter-datepicker" ).datepicker({ dateFormat: 'yy/m/d',
        numberOfMonths: 2,
        onSelect: function( selectedDate ) {
            if(!$(this).data().datepicker.first){
                $(this).data().datepicker.inline = true
                $(this).data().datepicker.first = selectedDate;
            }else{
                if(selectedDate > $(this).data().datepicker.first){
                    $(this).val($(this).data().datepicker.first+" - "+selectedDate);
                }else{
                    $(this).val(selectedDate+" - "+$(this).data().datepicker.first);
                }
                $(this).data().datepicker.inline = false;
            }
        },
        onClose:function(){
            delete $(this).data().datepicker.first;
            $(this).data().datepicker.inline = false;
            if ( $(this).val() !== 'Published date' && $(this).val() !== $(this).data().datepicker.lastVal ) {
                $(this).change();
            }
        }
 });

    //Hide alert text
    $(document).on('click', '.bb-alert-wrapper', function(e) {
        $(this).fadeOut();
        setTimeout(function() {
            $('.bb-alert-wrapper:hidden').remove();
        }, 3000);
    })

    //Library tabs functions
    $(document).on('click', '.bb-tab', function() {
        $('.bb-tab-area').hide();
        $('.bb-tab').removeClass('active');
        $(this).addClass('active');
        let elemID = $(this).attr('data-id');
        $('#' + elemID).show();
    });

    //Close modal functions
    $(document).on('click', '.bb-close-modal', function() {
        $(this).parents('.bb-content-modal-wrapper').remove();
    });

    //Close embed shortcode to editor functions
    $(document).on('click', '.bb-content-modal-wrapper .bb-video-wrap .bb-video-copy-btn, .bb-content-modal-wrapper .bb-playlist-wrap .bb-video-copy-btn, .bb-video-info-modal .bb-video-embedcode .bb-button', function(e) {
        e.preventDefault();
        let dataVideoId = '';
        if ( $(this).parents('.bb-video-wrap').length > 0 ) {
            dataVideoId = $(this).parents('.bb-video-wrap').attr('data-bb-video-id');
        }
        if ( $(this).parents('.bb-playlist-wrap').length > 0 ) {
            dataVideoId = $(this).parents('.bb-playlist-wrap').attr('data-bb-playlist-id');
        }
        if ( $(this).parents('.bb-video-info-modal').length > 0 ) {
            dataVideoId = $(this).parents('.bb-video-info-modal').attr('data-bb-info-modal-id');
        }
        saveToBufferText('[blue-billywig-embed videoId="' + dataVideoId + '"]');
    });

    //Modal with library button function
    $(document).on('click', '#bb-add-media-button', function() {
        if ( $('#wpwrap').length > 0 && $('#wpwrap .bb-content-modal-wrapper').length == 0 ) {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'blue_billywig_get_post_modal_template'
                },
                success: function(response) {
                    $('#wpwrap').append( response.data );

                    $( ".bb-filter-datepicker" ).datepicker({ dateFormat: 'yy/m/d',
                        numberOfMonths: 2,
                        onSelect: function( selectedDate ) {
                            if(!$(this).data().datepicker.first){
                                $(this).data().datepicker.inline = true
                                $(this).data().datepicker.first = selectedDate;
                            }else{
                                if(selectedDate > $(this).data().datepicker.first){
                                    $(this).val($(this).data().datepicker.first+" - "+selectedDate);
                                }else{
                                    $(this).val(selectedDate+" - "+$(this).data().datepicker.first);
                                }
                                $(this).data().datepicker.inline = false;
                            }
                        },
                        onClose:function(){
                            delete $(this).data().datepicker.first;
                            $(this).data().datepicker.inline = false;
                            if ( $(this).val() !== 'Published date' && $(this).val() !== $(this).data().datepicker.lastVal ) {
                                $(this).change();
                            }
                        }
                    });

                    if ( $('#wpwrap .bb-content-modal-wrapper #bb-videos').length > 0 ) {
                        loadCustomVideos(null, 0, true);
                    }
                    if ( $('#wpwrap .bb-content-modal-wrapper #bb-playlists').length > 0 ) {
                        loadCustomPaylists(null, true);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

    })

    //AJAX settings functions
    if ( $('.bb-settings-page-form') ) {
        $('.bb-settings-page-form').submit(function(e) {
            e.preventDefault();
            let thisForm = $(this);
            let hasErrors = false;
            thisForm.find('input, select').each(function() {
                let value = $(this).val();
                if (value === '') {
                    $(this).closest('.bb-label').addClass('error');
                    hasErrors = true;
                } else {
                    $(this).closest('.bb-label').removeClass('error');
                }
            });
            if (!hasErrors) {
                thisForm.addClass('bb-loading');
                const formData = thisForm.serializeArray();
                formData.push({name: 'action', value: 'blue_billywig_settings_submit_form'});
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: $.param(formData),
                    success: function(response) {
                        thisForm.removeClass('bb-loading');
                        showAlert('Data updated');
                        /*location.reload();*/
                    },
                    error: function(xhr, status, error) {
                        thisForm.removeClass('bb-loading');
                        console.log(error);
                    }
                });
            }
        });
    }

    //AJAX load library videos functions
    if ( $('#bb-playlists').length > 0 ) {
        loadCustomPaylists(null);
    }

    //Close modal
    $(document).on('click', '.bb-close-modal-btn', function() {
        $(this).parents('.bb-video-info-modal, .bb-playlist-info-modal').hide();
    })

    //Play video function
    $(document).on('click', '.bb-play-sign', function() {
        // console.log("Button CLick Umair");

        if ( $(this).parents('.bb-videos').length > 0 ) {
            let playId = $(this).parents('.bb-video-wrap').attr('data-bb-video-id');
            let playPubl = $(this).parents('.bb-video-wrap').attr('data-bb-video-publ');

            $(this).parent('.bb-video-item').find('.bb-thumb-img').hide();
            $(this).parent('.bb-video-item').append('<script type="text/javascript" src="https://' + playPubl + '.bbvms.com/p/default/c/' + playId + '.js" async="true"></script>');
            $(this).hide();
        }

        if ( $(this).parents('.bb-playlists').length > 0 ) {
            let playId = $(this).parents('.bb-playlist-wrap').attr('data-bb-playlist-id');
            let playPubl = $(this).parents('.bb-playlist-wrap').attr('data-bb-playlist-publ');

            $(this).parent('.bb-playlist-item').find('.bb-playlist-count').hide();
            $(this).parent('.bb-playlist-item').append('<script type="text/javascript" src="https://' + playPubl + '.bbvms.com/p/default/l/' + playId + '.js" async="true"></script>');
            $(this).hide();
        }
    })

    //AJAX delete library videos functions
    $(document).on('click', '.bb-video-delete', function(e) {
        let videoId = $(e.target).parents('.bb-video-info-modal').attr('data-bb-info-modal-id');
        let confirmData = confirm('Are you sure you want to delete the file?');
        if ( confirmData ) {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'blue_billywig_delete_library_videos',
                    videoId: videoId ,
                    'blue-billywig-nonce': blue_billywig_data.delete_nonce
                },
                success: function(response) {
                    if ( response.data.code == 200 && response.data.error == false ) {
                        showAlert('Was succesfully removed, after a while you will see the changes in the plugin.');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }
    })

    //AJAX save library videos functions
    $(document).on('click', '.bb-video-save', function(e) {
        let infoParent = $(this).parents('.bb-video-info-modal');
        console.log(infoParent);
        let infoTitle = infoParent.find('.bb-video-title input').val();
        let infoDesc = infoParent.find('.bb-video-description textarea').val();
        let infoTags = infoParent.find('.bb-video-tags input').val();
        let infoPlayout = infoParent.find('.bb-video-playout');

        const data = {
            action: 'blue_billywig_save_new_data_library_videos',
            'blue-billywig-nonce': blue_billywig_data.save_nonce,
            videoId: infoParent.attr('data-bb-info-modal-id'),
            newTitle: infoParent.find('.bb-video-title input').val(),
            newDesc: infoParent.find('.bb-video-description textarea').val(),
            newTags: infoParent.find('.bb-video-tags input').val(),
            newVideoPlayout: infoParent.find('.bb-video-playout .bb-filter-select').val(),
            updatedDate: new Date().toISOString()
        };

        infoParent.addClass('bb-loading');
        infoPlayout.hide();
        infoParent.find('.bb-video-embedcode').show();
        infoParent.find('.bb-video-meta .status-playout').show();
        infoParent.find('.bb-video-title').removeClass('edited').html(infoTitle);
        infoParent.find('.bb-video-description').removeClass('edited').html(infoDesc);
        infoParent.find('.bb-video-tags').removeClass('edited').html(infoTags);
        infoParent.find('.bb-controls').html('')
            .append('<button type="button" class="bb-btn-red bb-video-delete"><img src="' + blue_billywig_data.plugin_url + 'admin/assets/img/delete-icon.svg" alt="Delete video"> Delete</button>')
            .append('<button type="button" class="bb-btn-white bb-video-edit"><img src="' + blue_billywig_data.plugin_url + 'admin/assets/img/edit-icon.svg" alt="Edit video"> Edit</button>');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data,
            success: function(response) {
                infoParent.removeClass('bb-loading');
                showAlert('The data is updated, after a while you will see the changes in the plugin.');
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    })

    //Library videos functions
    $(document).on('click', '.bb-video-wrap', function(e) {
        $('.bb-video-info-modal').hide();
        let videoId = $(e.target).parents('.bb-video-wrap').attr('data-bb-video-id');
        $('.bb-video-info-modal[data-bb-info-modal-id="'+ videoId +'"]').css('display', 'flex');
    })
    $(document).on('click', '.bb-video-edit', function(e) {
        let infoParent = $(e.target).parents('.bb-video-info-modal');
        let infoTitle = infoParent.find('.bb-video-title').text();
        let infoDesc = infoParent.find('.bb-video-description').text();
        let infoTags = infoParent.find('.bb-video-tags').text();
        let infoPlayout = infoParent.find('.bb-video-playout');

        infoPlayout.show();
        infoParent.find('.bb-video-embedcode').hide();
        infoParent.find('.bb-video-meta .status-playout').hide();
        infoParent.find('.bb-video-title').addClass('edited').html('<span>Title</span><input type="text" name="video-title" value="'+ infoTitle +'">');
        infoParent.find('.bb-video-description').addClass('edited').html('<span>Description</span><textarea name="video-desc" rows="3">'+ infoDesc +'</textarea>');
        infoParent.find('.bb-video-tags').addClass('edited').html('<span>Tags</span><input type="text" name="video-tags"  value="'+ infoTags +'">');
        infoParent.find('.bb-controls').html('')
            .append('<button type="button" class="bb-btn-white bb-video-cancel"><img src="' + blue_billywig_data.plugin_url + 'admin/assets/img/cancel-icon.svg" alt="Cancel edit"> Cancel</button>')
            .append('<button type="button" class="bb-btn-blue bb-video-save"><img src="' + blue_billywig_data.plugin_url + 'admin/assets/img/save-icon.svg" alt="Save data"> Save</button>');
    })
    $(document).on('click', '.bb-video-cancel', function(e) {
        let infoParent = $(e.target).parents('.bb-video-info-modal');
        let infoTitle = infoParent.find('.bb-video-title input').val();
        let infoDesc = infoParent.find('.bb-video-description textarea').val();
        let infoTags = infoParent.find('.bb-video-tags input').val();
        let infoPlayout = infoParent.find('.bb-video-playout');

        infoPlayout.hide();
        infoParent.find('.bb-video-embedcode').show();
        infoParent.find('.bb-video-meta .status-playout').show();
        infoParent.find('.bb-video-title').removeClass('edited').html(infoTitle);
        infoParent.find('.bb-video-description').removeClass('edited').html(infoDesc);
        infoParent.find('.bb-video-tags').removeClass('edited').html(infoTags);
        infoParent.find('.bb-controls').html('')
            .append('<button type="button" class="bb-btn-red bb-video-delete"><img src="' +  blue_billywig_data.plugin_url + 'admin/assets/img/delete-icon.svg" alt="Delete video"> Delete</button>')
            .append('<button type="button" class="bb-btn-white bb-video-edit"><img src="' + blue_billywig_data.plugin_url + 'admin/assets/img/edit-icon.svg" alt="Edit video"> Edit</button>');
    })

    //Library playlist functions
    $(document).on('click', '.bb-playlist-wrap', function() {
        $('.bb-playlist-info-modal').hide();
        let playlistId = $(this).parent('.bb-playlist-wrap').attr('data-bb-playlist-id');
        $('.bb-playlist-info-modal[data-bb-info-modal-id="'+ playlistId +'"]').show();
    })

    //Upload functions
    let globalUploadData = new FormData();
    $(document).on('change', '.bb-drop-form input[type="file"]', function(e) {
        e.stopPropagation();
        e.preventDefault();
        let file_data = $(this).prop('files')[0];

        globalUploadData.delete('file');
        globalUploadData.append('file', file_data);

        if ( $('.bb-drop-metadata input[name="bb-drop-title"]') ) $('.bb-drop-metadata input[name="bb-drop-title"]').val( file_data.name ); $(this).next('span').text('Select another file'); $('.bb-drop-wrapper.bb-drop-form-submit').slideDown();

    });
    $(document).on('submit', '.bb-drop-metadata', function(e) {
        e.stopPropagation();
        e.preventDefault();

        if ( $('.bb-drop-form') ) $('.bb-drop-form').removeClass('bb-loading');
        let dropTitle = $(this).find('input[name="bb-drop-title"]').val();
        let dropDescription = $(this).find('textarea[name="bb-upload-description"]').val();
        let dropTags = $(this).find('input[name="bb-drop-tags"]').val();
        let dropStatus = $(this).find('select[name="bb-upload-status"]').val();
        globalUploadData.delete('action');
        globalUploadData.append('action', 'blue_billywig_upload_videos_server');
        globalUploadData.append('blue-billywig-nonce', blue_billywig_data.upload_nonce);

        console.log(globalUploadData);

        if ( dropTitle.length > 0 ) { globalUploadData.delete('newTitle'); globalUploadData.append('newTitle', dropTitle) } else { globalUploadData.delete('newTitle') };
        if ( dropDescription.length > 0 ) { globalUploadData.delete('newDescription'); globalUploadData.append('newDescription', dropDescription) } else { globalUploadData.delete('newDescription') };
        if ( dropTags.length > 0 ) { globalUploadData.delete('newTags'); globalUploadData.append('newTags', dropTags) } else { globalUploadData.delete('newTags') };
        if ( dropStatus.length > 0 ) { globalUploadData.delete('newStatus'); globalUploadData.append('newStatus', dropStatus) } else { globalUploadData.delete('newStatus') };

        $('.bb-upload-video-form').addClass('bb-loading');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: globalUploadData,
            success: function(response) {
                if ( !response.error ) {
                    showAlert('File uploading and transcoding to the web wite, wait for upload at bbvms.com');
                    let data = response.success.data;
                    let new_data = response.success.newData;
                    let title = response.success.title.replace(/\.[^/.]+$/, "");
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'blue_billywig_upload_videos_account',
                            'blue-billywig-nonce': globalUploadData.get('blue-billywig-nonce'),
                            videoPath: data,
                            ...new_data
                        },
                        success: function(response) {
                            if ( response ) {
                                showAlert('File uploading and transcoding to bbvms.com, after a while you will see the changes in the plugin.');
                                setTimeout(function() {
                                    $.ajax({
                                        url: ajaxurl,
                                        type: 'POST',
                                        data: {
                                            action: 'blue_billywig_remove_uploaded_file',
                                            'blue-billywig-nonce': globalUploadData.get('blue-billywig-nonce'),
                                            filePath: data
                                        },
                                        success: function(response) {
                                            $('.bb-drop-wrapper.bb-drop-form-submit').hide();
                                            $('.bb-upload-video-form').removeClass('bb-loading');
                                        },
                                        error: function(xhr, status, error) {
                                            console.log(error);
                                            $('.bb-upload-video-form').removeClass('bb-loading');
                                        }
                                    });
                                }, 5000)
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                            $('.bb-upload-video-form').removeClass('bb-loading');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                $('.bb-upload-video-form').removeClass('bb-loading');
            }
        });
    });

    if ( $('.bb-filters-upload .bb-upload-input') ) {
        $('.bb-filters-upload .bb-upload-input').on('click', function() {
            $(this).parents('.bb-tab-area').find('.bb-upload-video-form').toggle();
        })
    }

    //Filter videos
    loadFilteredData('#bb-videos .bb-filters-search input[name="bb-search-video"], #bb-videos .bb-filters-search input[name="bb-video-published-date"], #bb-videos .bb-filters-search select[name="bb-video-media-type"]', 'videos');

    //Filter playlists
    loadFilteredData('#bb-playlists .bb-filters-search input[name="bb-search-playlists"], #bb-playlists .bb-filters-search input[name="bb-playlists-published-date"]', 'playlists');

    //Pagination videos
    if ( $('.bb-pagination-video .bb-pages') ) {
        $(document).on('click', '.bb-pagination-video .bb-pages .bb-page-btn', function() {
            let targetPage = $(this).attr('data-bb-page');
            loadCustomVideos(null, targetPage);
        })
    }

    $(document).on('click', 'button[name="bb-upload-clear"]', function() {
        let parent = $(this).parents('.bb-tab-area');
        let parentId = parent.attr('id');
        $('.bb-filters-search input[name="bb-search-video"], .bb-filters-search input[name="bb-search-playlists"]').val('').attr('value', '');
        $('.bb-filters-search input.bb-filter-datepicker').val('Published date').attr('value', 'Published date');
        $('.bb-filters-search .bb-filter-select').val('all').attr('value', 'all');
        $('#' + parentId).addClass('bb-loading');
        if ( parent.attr('id') === 'bb-playlists' ) {
            loadCustomPaylists(null);
        }
        if ( parent.attr('id') === 'bb-videos' ) {
            loadCustomVideos(null, 0);
        }
        $(this).hide();
    })

    //AJAX load library videos functions
    if ( $('#bb-videos').length > 0 ) {
        loadCustomVideos(null, 0);
    }

    function loadFilteredData(item, type) {
        $(document).on('change', item, function() {
            let filtersSearchDataQuary = new Map();
            let parent = $(this).parents('.bb-tab-area');
            let parentId = parent.attr('id');
            let clearBtn = parent.find('button[name="bb-upload-clear"]');
            $('.bb-pagination-video .bb-pages').html('');

            $('#' + parentId).addClass('bb-loading');
            clearBtn.hide();
            $(item).each(function() {
                if ( $(this).attr('name') === 'bb-video-media-type' ) {
                    filtersSearchDataQuary.set('mediaType', $(this).val());
                    if ( $(this).val() !== 'all' ) {
                        clearBtn.show();
                    }
                }
                if ( $(this).attr('name') === 'bb-search-video' || $(this).attr('name') === 'bb-search-playlists' ) {
                    if ( $(this).val().length > 2 ) {
                        filtersSearchDataQuary.set('title', $(this).val());
                        clearBtn.show();
                    } else {
                        filtersSearchDataQuary.delete('title');
                    }
                }
                if ( $(this).attr('name') === 'bb-video-published-date' || $(this).attr('name') === 'bb-playlists-published-date' ) {

                    if ( $(this).val() !== 'Published date' && $(this).val().length > 0 ) {
                        let dates = $(this).val().split(' ').join('').split('-');

                        let pubDateYear = new Date( dates[0] ).getFullYear();
                        let pubDateMonth = new Date( dates[0] ).getMonth() + 1;
                        pubDateMonth = pubDateMonth < 10 ? '0' + pubDateMonth : pubDateMonth;
                        let pubDateDate = new Date( dates[0] ).getDate();
                        pubDateDate = pubDateDate < 10 ? '0' + pubDateDate : pubDateDate;

                        let newFilterDateFrom =  pubDateYear + '-' + pubDateMonth + '-' + pubDateDate;
                        let newFilterDateTo =  pubDateYear + '-' + pubDateMonth + '-' + pubDateDate;

                        if ( dates.length > 1 ) {
                            let pubDateYear2 = new Date( dates[1] ).getFullYear();
                            let pubDateMonth2 = new Date( dates[1] ).getMonth() + 1;
                            pubDateMonth2 = pubDateMonth2 < 10 ? '0' + pubDateMonth2 : pubDateMonth2;
                            let pubDateDate2 = new Date( dates[1] ).getDate();
                            pubDateDate2 = pubDateDate2 < 10 ? '0' + pubDateDate2 : pubDateDate2;
                            newFilterDateTo =  pubDateYear2 + '-' + pubDateMonth2 + '-' + pubDateDate2;
                        }




                        filtersSearchDataQuary.set('createddate', '[' + newFilterDateFrom + 'T00:00:00Z TO ' + newFilterDateTo + 'T22:59:59Z]')
                        clearBtn.show();
                    } else {
                        filtersSearchDataQuary.delete('createddate');
                    }
                }
            })

            filtersSearchData = Object.fromEntries(filtersSearchDataQuary);

            if ( type === 'videos') {
                loadCustomVideos(filtersSearchData, 0);
                if ( $('.bb-pagination-video .bb-pages') ) {
                    $(document).on('click', '.bb-pagination-video .bb-pages .bb-page-btn', function() {
                        let targetPage = $(this).attr('data-bb-page');
                        loadCustomVideos(filtersSearchData, targetPage);
                    })
                }
            }
            if ( type === 'playlists') {
                loadCustomPaylists(filtersSearchData);
            }
        })
    }

    function loadCustomVideos(quary, page, modal = null) {
        ajaxurl += "?noCache=" + (new Date().getTime()) + Math.random();
        dataArray = {
            action: 'blue_billywig_load_library_videos',
            'blue-billywig-nonce': blue_billywig_data.load_videos_nonce
        };

        if ( quary !== null ) {
            dataArray.quary = quary;
        }
        if ( page !== null ) {
            dataArray.page = page;
        }
        if ( modal !== null ) {
            dataArray.modal = modal;
        }

        $('#bb-videos').addClass('bb-loading');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            cache: false,
            data: dataArray,
            success: function(response) {
                if ( response.error ) {
                    if ( $('#bb-videos .bb-videos') ) {
                        $('#bb-videos .bb-videos').html('<p>Request Error! Try again later or contact support.</p>');
                        $('#bb-videos').removeClass('bb-loading');
                    }
                } else {
                    if ( $('#bb-videos .bb-videos') ) {
                        if ( response.videoData.length > 1 ) {
                            $('#bb-videos .bb-videos').html('');
                            setTimeout(function() {
                                $('#bb-videos').removeClass('bb-loading');
                                $('#bb-videos .bb-videos').html(response.videoData)
                                $('#bb-videos .bb-count span').html(response.videoFullCount)
                                if ( response.publicationPath && $('.bb-content-modal-wrapper .bb-tabs .bb-last-tab-btn a') ) {
                                    $('.bb-content-modal-wrapper .bb-tabs .bb-last-tab-btn a').attr('href', 'https:' + response.publicationPath + '/ovp/#/login')
                                }

                                if ( ( +response.pagination_limit < +response.videoFullCount ) && $('.bb-pagination-video .bb-pages') ) {
                                    $('.bb-pagination-video .bb-pages').html('');
                                    for (let i = 0; i < Math.ceil(response.videoFullCount / +response.pagination_limit); i++) {
                                        let pageActive = page == i ? ' active' : '';
                                        $('.bb-pagination-video .bb-pages').append('<li class="bb-page-btn' + pageActive + '" data-bb-page="' + i + '">' + (i + 1) + '</li>');
                                    }
                                }
                            },1000)
                        } else {
                            $('#bb-videos .bb-count span').html('0');
                            $('#bb-videos .bb-videos').html('<p>No data to show</p>');
                            $('#bb-videos').removeClass('bb-loading');
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                if ( $('#bb-videos .bb-videos') ) {
                    $('#bb-videos .bb-videos').html('<p>Request Error! Try again later or contact support.</p>');
                    $('#bb-videos').removeClass('bb-loading');
                }
            }
        });
    }

    function loadCustomPaylists(quary, modal = null) {
        ajaxurl += "?noCache=" + (new Date().getTime()) + Math.random();
        dataArray = {
            action: 'blue_billywig_load_library_playlists',
            'blue-billywig-nonce': blue_billywig_data.load_playlists_nonce
        };
        if ( quary !== null ) {
            dataArray.quary = quary;
        }
        if ( modal !== null ) {
            dataArray.modal = modal;
        }
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            cache: false,
            data: dataArray,
            success: function(response) {
                if ( response.error ) {
                    if ( $('#bb-playlists .bb-playlists') ) {
                        $('#bb-playlists .bb-playlists').html('<p>Request Error! Try again later or contact support.</p>');
                        $('#bb-playlists').removeClass('bb-loading');
                    }
                } else {
                    if ( $('#bb-playlists .bb-playlists') ) {
                        if ( response.playlistData.length > 1 ) {
                            $('#bb-playlists .bb-playlists').html('');
                            setTimeout(function() {
                                $('#bb-playlists').removeClass('bb-loading');
                                $('#bb-playlists .bb-playlists').html(response.playlistData);
                                $('#bb-playlists .bb-count span').html(response.playlistCount);
                            },1000)
                        } else {
                            $('#bb-playlists .bb-playlists').html('<p>No data to show</p>');
                            $('#bb-playlists .bb-count span').html('0');
                            $('#bb-playlists').removeClass('bb-loading');
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                if ( $('#bb-playlists .bb-playlists') ) {
                    $('#bb-playlists .bb-playlists').html('<p>Request Error! Try again later or contact support.</p>');
                    $('#bb-playlists').removeClass('bb-loading');
                }
                console.log(error);
            }
        });
    }

    function showAlert(text) {
        let alertIndex = 1;
        if ( +$('.bb-alert-wrapper:last-of-type').attr('data-bb-alert-id') >= 1 ) {
            alertIndex = +$('.bb-alert-wrapper:last-of-type').attr('data-bb-alert-id') + 1;
        }
        $('.bb-content-wrapper').append('<div class="bb-alert-wrapper hide" data-bb-alert-id="' + alertIndex + '">' + text + '</div>');
        if ( $('.bb-alert-wrapper').length > 0 ) {
            $('.bb-content-wrapper').find('.bb-alert-wrapper:not(.hide)').each(function(index) {
                $(this).css('transform', 'translateY(' + ( ( $('.bb-content-wrapper .bb-alert-wrapper.hide').outerHeight() + 10 ) * ( index + 1 ) + 'px' )  + ')');
                setTimeout(function() {
                    if ( $('.bb-alert-wrapper[data-bb-alert-id="' + alertIndex + '"]') ) {
                        $('.bb-alert-wrapper[data-bb-alert-id="' + alertIndex + '"]').remove();
                    }
                }, 30000);
            });
        }
        if ( alertIndex === 1 ) {
            setTimeout(function() {
                if ( $('.bb-alert-wrapper[data-bb-alert-id="1"]') ) {
                    $('.bb-alert-wrapper[data-bb-alert-id="1"]').remove();
                }
            }, 30000);
        }
        $('.bb-content-wrapper .bb-alert-wrapper.hide').removeClass('hide');
    }

    function saveToBufferText(text) {
      const shadowInput = document.createElement('input');
      shadowInput.value = text;
      document.body.appendChild(shadowInput);
      shadowInput.select();
      if (document.execCommand('copy')) {
        showAlert('Paste the code in the article where you\'d like it to appear.');

      } else {
        showAlert('Error shortcode generate');
      }
      document.body.removeChild(shadowInput)
    }
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) {
        return '0';
    } else {
        var k = 1024;
        var dm = decimals < 0 ? 0 : decimals;
        var sizes = ['b', 'kb', 'mb', 'gb', 'tg'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
}