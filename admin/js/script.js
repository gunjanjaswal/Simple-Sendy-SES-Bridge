jQuery(document).ready(function ($) {
    const selectedPosts = [];
    let bannerUrl = '';
    let layoutType = 'list';

    // Banner Image Upload
    $('#sssb-upload-banner').on('click', function (e) {
        e.preventDefault();
        const image_frame = wp.media({
            title: 'Select Banner Image',
            multiple: false,
            library: { type: 'image' },
            button: { text: 'Use Banner' }
        });

        image_frame.on('select', function () {
            const uploaded_image = image_frame.state().get('selection').first().toJSON();
            bannerUrl = uploaded_image.url;
            $('#sssb-banner-url').val(bannerUrl);
            $('#sssb-banner-preview').html(`<img src="${bannerUrl}" style="max-width:100%; height:auto;">`);
            $('#sssb-remove-banner').show();
            $('#sssb-upload-banner').text('Change Banner');
            updatePreview();
        });

        image_frame.open();
    });

    $('#sssb-remove-banner').on('click', function (e) {
        e.preventDefault();
        bannerUrl = '';
        $('#sssb-banner-url').val('');
        $('#sssb-banner-preview').empty();
        $(this).hide();
        $('#sssb-upload-banner').text('Select Banner');
        updatePreview();
    });

    // Layout Change
    $('input[name="sssb_layout"]').on('change', function () {
        layoutType = $(this).val();
        updatePreview();
    });

    // Search Posts
    $('#sssb-search').on('input', function () {
        const query = $(this).val();
        if (query.length < 3) return;

        $.ajax({
            url: sssb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'sssb_search_posts',
                nonce: sssb_ajax.nonce,
                query: query
            },
            success: function (response) {
                if (response.success) {
                    renderPostList(response.data);
                }
            }
        });
    });

    // Add Post to selection
    $(document).on('click', '.sssb-add-post', function (e) {
        e.preventDefault();
        const postId = $(this).data('id');
        const title = $(this).data('title');
        const thumbnail = $(this).data('thumbnail');
        const excerpt = $(this).data('excerpt');
        const link = $(this).data('link');
        const content = $(this).data('content');

        if (selectedPosts.some(p => p.id === postId)) return;

        selectedPosts.push({ id: postId, title, thumbnail, excerpt, link, content });
        renderSelectedPosts();
        updatePreview();
    });

    // Remove Post
    $(document).on('click', '.sssb-remove-post', function (e) {
        e.preventDefault();
        const postId = $(this).data('id');
        const index = selectedPosts.findIndex(p => p.id === postId);
        if (index > -1) {
            selectedPosts.splice(index, 1);
            renderSelectedPosts();
            updatePreview();
        }
    });

    // Create Campaign
    $('#sssb-create-campaign').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.prop('disabled', true).text('Creating...');

        const campaignData = {
            subject: $('#sssb-subject').val(),
            from_name: $('#sssb-from-name').val(),
            from_email: $('#sssb-from-email').val(),
            html_text: $('#sssb-preview-content').html(),
            list_id: $('#sssb-list-id').val(),
            send_type: $('input[name="sssb_send_type"]:checked').val()
        };

        campaignData.plain_text = $(campaignData.html_text).text();

        $.ajax({
            url: sssb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'sssb_create_campaign',
                nonce: sssb_ajax.nonce,
                campaign: campaignData
            },
            success: function (response) {
                $btn.prop('disabled', false).text('Create Campaign');
                if (response.success) {
                    alert('Campaign Created Successfully: ' + response.data.message);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function () {
                $btn.prop('disabled', false).text('Create Campaign');
                alert('Connection error');
            }
        });
    });

    function renderPostList(posts) {
        let html = '';
        posts.forEach(post => {
            html += `
                <div class="sssb-post-item">
                    <img src="${post.thumbnail}" alt="">
                    <div>
                        <strong>${post.title}</strong>
                        <br>
                        <button class="button button-small sssb-add-post" 
                            data-id="${post.id}" 
                            data-title="${post.title}" 
                            data-thumbnail="${post.thumbnail}" 
                            data-excerpt="${post.excerpt}"
                            data-link="${post.link}">Add</button>
                    </div>
                </div>
            `;
        });
        $('#sssb-post-results').html(html);
    }

    function renderSelectedPosts() {
        let html = '';
        selectedPosts.forEach(post => {
            html += `
                <div class="sssb-selected-item">
                    <span>${post.title}</span>
                    <a href="#" class="sssb-remove-post" data-id="${post.id}">Remove</a>
                </div>
            `;
        });
        $('#sssb-selected-list').html(html);
    }

    function updatePreview() {
        let html = '';

        // Container
        html += '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;">';

        // Banner
        if (bannerUrl) {
            html += `<div style="margin-bottom: 20px;"><img src="${bannerUrl}" style="max-width: 100%; width: 100%; height: auto; display: block; border-radius: 4px;"></div>`;
        }

        // Post Loop
        if (layoutType === 'grid') {
            // Mobile Responsive Grid using inline-block stacking
            html += '<div style="text-align: center;">';
            selectedPosts.forEach((post) => {
                html += `
                    <div style="display: inline-block; width: 100%; max-width: 280px; vertical-align: top; margin: 0 5px 20px 5px; text-align: left;">
                        ${post.thumbnail ? `<a href="${post.link}"><img src="${post.thumbnail}" style="width: 100%; height: auto; display: block; margin-bottom: 10px; border-radius: 4px;"></a>` : ''}
                        <h3 style="font-size: 18px; margin: 0 0 10px 0; line-height: 1.3;"><a href="${post.link}" style="text-decoration: none; color: #333;">${post.title}</a></h3>
                        <p style="font-size: 14px; color: #666; margin: 0 0 10px 0;">${post.excerpt}</p>
                        <a href="${post.link}" style="display: inline-block; font-size: 13px; color: #0073aa; text-decoration: none;">Read More &rarr;</a>
                    </div>
                `;
            });
            html += '</div>';
        } else if (layoutType === 'full') {
            selectedPosts.forEach(post => {
                html += `
                    <div class="sssb-email-preview-item" style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee;">
                        <h2 style="font-size: 24px; margin: 0 0 15px 0;"><a href="${post.link}" style="text-decoration: none; color: #333;">${post.title}</a></h2>
                        ${post.thumbnail ? `<a href="${post.link}"><img src="${post.thumbnail}" style="width: 100%; height: auto; display: block; margin-bottom: 15px; border-radius: 4px;"></a>` : ''}
                        <div style="font-size: 16px; line-height: 1.6; color: #444;">${post.excerpt}</div>
                        <div style="margin-top: 15px;">
                            <a href="${post.link}" style="display: inline-block; padding: 10px 20px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 4px;">Read Full Article</a>
                        </div>
                    </div>
                `;
            });
        } else {
            // Default List - Responsive using max-width for image and content
            selectedPosts.forEach(post => {
                html += `
                    <div class="sssb-email-preview-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                         ${post.thumbnail ? `
                         <div style="display: inline-block; width: 100%; max-width: 150px; vertical-align: top; margin-right: 20px; margin-bottom: 10px;">
                            <a href="${post.link}"><img src="${post.thumbnail}" style="width: 100%; height: auto; display: block; border-radius: 4px;"></a>
                         </div>` : ''}
                        <div style="display: inline-block; width: 100%; max-width: 400px; vertical-align: top;">
                            <h2 style="font-size: 20px; margin: 0 0 10px 0;"><a href="${post.link}" style="text-decoration: none; color: #333;">${post.title}</a></h2>
                            <p style="font-size: 14px; color: #555; margin: 0 0 10px 0;">${post.excerpt}</p>
                            <a href="${post.link}" style="display: inline-block; font-size: 14px; color: #0073aa; text-decoration: none;">Read More</a>
                        </div>
                    </div>
                `;
            });
        }

        html += '</div>';
        $('#sssb-preview-content').html(html);
    }
});
