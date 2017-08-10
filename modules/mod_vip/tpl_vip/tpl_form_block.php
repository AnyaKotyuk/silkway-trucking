<?
var_dump($multi);
?>
<from class="form tour-reseration vip-request" action="find-excursion" method="post">
    <h3 class="block-title">VIP reservation form</h3>
    <p class="text">Lorem ipsum dolor sit amet, <b>consectetur adipiscing</b> elit. In suscipit felis eget facilisis mollis. Suspendisse interdum placerat arcu, eu aliquam justo varius ac.</p>

    <!-- LEFT SIDE OF FORM -->
    <div class="left-side">
        <div class="input select">
            <div class="input date">
                <label for="choose-date">Select date</label>
                <input type="date" value="" name="choose-date" id="choose-date" class="input-date" />
            </div>
        </div>
        <div class="input">
            <label>Your Name</label>
            <input type="text" name="name" class="excurs-user-name" value="Input Your Name " />
        </div>
        <div class="input">
            <label>Your Email</label>
            <input type="email" name="name" class="excurs-user-email" value="Input Your Email" />
        </div>
        <div class="input">
            <label>Your phone</label>
            <input type="text" name="phone" class="excurs-user-phone" value="Input Your phone" />
        </div>
    </div>
    <!-- END LEFT SIDE OF FORM -->

    <!-- RIGHT SIDE OF FORM -->
    <div class="right-side">
        <div class="input">
            <label>Airport Departure</label>
            <input type="text" name="name" class="excurs-user-name" value="Input airport Departure" />
        </div>

        <div class="input">
            <label class="two-line-label">Flight number</label>
            <input type="text" name="name" class="excurs-user-name" value="Input Flight number" />
        </div>
        <div class="input">
            <label>Number of persons</label>
            <select class="excursion-num">
                <option value="1">2 persons</option>
                <option value="1">3 persons</option>
                <option value="1">4 persons</option>
                <option value="1">5 persons</option>
                <option value="1">6 persons</option>
                <option value="1">7 persons</option>
                <option value="1">8 persons</option>
                <option value="1">9 persons</option>
                <option value="1">10 persons</option>
                <option value="1">> 10 persons</option>
            </select>
        </div>
        <div class="input">
            <label>Your country</label>
            <input type="text" name="name" class="excurs-user-name" value="Input Your country" />
        </div>

    </div>
    <!-- END LEFT SIDE OF FORM -->
    <div class="input textarea">
        <label>Comments</label>
        <textarea name="comments">во всех формах бронирования испрользовать google recapcha  https://www.google.com/recaptcha/intro/index.html</textarea>
    </div>
    <div class="submit">
        <input type="submit" class="" value="Submit" />
    </div>
</from>