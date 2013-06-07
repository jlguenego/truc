<div class="evt_title"><p>{{Sign in}}</p></div>

<table class="evt_sign_in_table">
	<tr>
		<td class="evt_sign_in_form" width="450">
		<div class="evt_sign_in_table_title">{{Easy connect with}}</div>
<?php
	$redirect = '';
	if (isset($_GET["redirect"])) {
		$redirect = "&amp;redirect=yes";
	}
	$images_dir = SKIN_DIR.'/images/openid';
?>
<form id="openid_form" method="post" action="?action=openid_authenticate<?php echo $redirect; ?>" class="openid">
	<div>
		<ul id="providers" class="providers" style="">
			<li id="openid" title="OpenID" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/openidW.png">
				<span class="url" style="display: none;"> <strong width="300">http://</strong> </span>
				<span class="label" style="display: none;">Enter your <a href="http://openid.net/" class="openid_logo" target="_blank">OpenId</a></span>
			</li>
			<li id="google" title="Google" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/googleW.png">
				<span class="url" style="display: none;">https://www.google.com/accounts/o8/id</span>
			</li>
			<li id="yahoo" title="Yahoo" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/yahooW.png">
				<span class="url" style="display: none;">http://me.yahoo.com/</span>
			</li>
			<li id="aol" title="AOL" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/aolW.png">
				<span class="url" style="display: none;">http://openid.aol.com/<strong>username</strong></span>
				<span class="label" style="display: none;">Enter your AOL screen name</span>
			</li>
			<li id="myopen" title="MyOpenID" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/myopenid.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.myopenid.com/</span>
				<span class="label" style="display: none;">Enter your MyOpenID user name</span>
			</li>
			<li id="flikr" title="Flickr" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/flickr.png">
				<span class="url" style="display: none;">http://flickr.com/<strong>username</strong>/</span>
				<span class="label" style="display: none;">Enter your Flickr user name</span>
			</li>
			<li id="thechnorati" title="Technorati" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/technorati.png">
				<span class="url" style="display: none;">http://technorati.com/people/technorati/<strong>username</strong>/</span>
				<span class="label" style="display: none;">Enter your Technorati user name</span>
			</li>
			<li id="wordpress" title="Wordpress" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/wordpress.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.wordpress.com</span>
				<span class="label" style="display: none;">Enter your Wordpress blog name</span>
			</li>
			<li id="blogger" title="Blogger" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/blogger.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.blogspot.com/</span>
				<span class="label" style="display: none;">Enter your Blogger blog name</span>
			</li>
			<li title="LiveJournal" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/livejournal.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.livejournal.com</span>
				<span class="label" style="display: none;">Enter your LiveJournal blog name</span>
			</li>
			<li id="claimid" title="ClaimID" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/claimid.png">
				<span class="url" style="display: none;">http://claimid.com/<strong>username</strong></span>
				<span class="label" style="display: none;">Enter your ClaimID user name</span>
			</li>
			<li id="vidoop" title="Vidoop" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/vidoop.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.myvidoop.com/</span>
				<span class="label" style="display: none;">Enter your Vidoop user name</span>
			</li>
			<li id="verisign" title="Verisign" class="provider" style="line-height: 0; cursor: pointer;">
				<img alt="icon" src="<?php echo $images_dir; ?>/verisign.png">
				<span class="url" style="display: none;">http://<strong>username</strong>.pip.verisignlabs.com/</span>
				<span class="label" style="display: none;">Enter your Verisign user name</span>
			</li>
		</ul>
	</div>
	<table id="openid_url" width="500">
		<tr>
			<td colspan="2">
				<label for="openid_identifier"></label>
			</td>
		</tr>
		<tr>
			<td>
				<div class="inline">
					<span id="url_prefix"></span>
					<input id="openid_identifier_visible" placeholder="" name="openid_identifier_visible" type="text">
					<span id="url_suffix"></span>
					<input id="url" name="url" type="hidden">
				</div>
			</td>
			<td width="100">
				<input type="button" id="openid_login_button" class="evt_button evt_btn_small" value="{{Login}}" />
			</td>
		</tr>
	</table>
</form>
		</td>
		<td width="200" align="center">
			&nbsp;
		</td>
		<td class="evt_sign_in_form">
<div class="evt_sign_in_table_title">{{Password connect}}</div>
<?php
	$f = new Form();
	$f->action = "?action=authenticate";
	if (isset($_GET["redirect"])) {
		$f->action .= "&amp;redirect=yes";
	}
	$f->other_attrs = 'id="sign_in"';
	$f->method = "POST";
	$f->add_text(_t("Email"), "email", default_value("email"), _t("Enter your email"));
	$f->add_password(_t("Password"), "clear_password", _t("Enter your password"));
	$f->add_hidden("password", "");
	$f->add_submit(_t("Sign in"));
	echo $f->html();
?>
<br/>
<br/>
<div class="form_cancel">{{Not registered? Then}} <a href="index.php?action=get_form&amp;type=account">{{create an account}}</a></div>
<a href="?action=get_form&type=forgotten_password">{{Forgot your password?}}</a><br/>
		</td>
	</tr>
</table>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_password', 'password');
		$('#openid').click();
	});
	$("#sign_in").submit(function() {
		$('input[type=password]').attr('name', '');
	});

	//OPENID
	$('.provider').click(function() {
		log('click');
		var direct_list = ['google', 'yahoo'];
		var direct = ($.inArray($(this).attr('id'), direct_list) != -1);
		if (direct) {
			log('direct');
			$('#url').val($(this).find('.url').html());
			log($('#url').val());
			$('#openid_form').submit();
			return;
		}
		$('label[for=openid_identifier]').html($(this).find('.label').html());
		var url = $(this).find('.url');

		var prefix = $.parseHTML(url.html())[0];
		log(prefix);
		var suffix = $.parseHTML(url.html())[2];
		var field = url.find('strong').html();
		var field_width = url.find('strong').attr('width') || 90;
		log(field_width);
		log(field);
		$('#url_prefix').html(prefix);
		$('#openid_identifier_visible').css('width', field_width + 'px');
		$('#openid_identifier_visible').attr('placeholder', field);
		$('#url_suffix').html(suffix);
	});

	$('#openid_login_button').click(function() {
		var username = $('#openid_identifier_visible').val();
		username = $.trim(username);
		if (username == '') {
			alert('{{Please insert a correct OpenId}}');
			return;
		}
		var url = $('#url_prefix').html() + username +$('#url_suffix').html();
		url = $.trim(url);
		$('#url').val(url);
		log($('#url').val());
		$('#openid_form').submit();
		return;
	});
</script>