<footer class="footer">
    <div class="uk-container uk-container-center">
        <div class="footer-upper">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-5">
                    <div class="footer-contact">
                        <a href="" class="image img-scaledown"><img src="{{ asset($system['homepage_logon']) }}"
                                alt=""></a>
                        <div class="footer-slogan">Awesome grocery store website template</div>
                        <div class="company-address">
                            <div class="address">{{ $system['contact_address'] }}</div>
                            <div class="phone">Hotline: {{ $system['contact_technical_phone'] }}</div>
                            <div class="email">{{ $system['contact_email'] }}</div>
                            {{-- <div class="hour">{{ $system['contact_address'] }}</div> --}}
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-3-5">
                    <div class="footer-menu">
                        <div class="uk-grid uk-grid-medium">
                            <?php for($i = 0; $i<=3; $i++){  ?>
                            <div class="uk-width-large-1-4">
                                <div class="ft-menu">
                                    <div class="heading">Company</div>
                                    <ul class="uk-list uk-clearfix">
                                        <li><a href="">About Us</a></li>
                                        <li><a href="">Delivery Information</a></li>
                                        <li><a href="">Privacy Policy</a></li>
                                        <li><a href="">Term & Conditions</a></li>
                                        <li><a href="">Contact us</a></li>
                                        <li><a href="">Support Center</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php }  ?>
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-1-5">
                    <div class="fanpage-facebook">
                        <div class="ft-menu">
                            <div class="heading">Fanpage Facebook</div>
                            <div class="fanpage">
                                <div class="fb-page" data-href="{{ $system['social_facebook'] }}" data-tabs=""
                                    data-width="" data-height="" data-small-header="false"
                                    data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                                    <blockquote cite="{{ $system['social_facebook'] }}" class="fb-xfbml-parse-ignore">
                                        <a href="{{ $system['social_facebook'] }}">Facebook</a>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="copyright-text">{!! $system['homepage_copyright'] !!}</div>
                <div class="copyright-contact">
                    <div class="uk-flex uk-flex-middle">
                        <div class="phone-item">
                            <div class="p">Hotline: {{ $system['contact_technical_phone'] }}</div>
                            <div class="worktime">Làm việc: 8:00 - 22:00</div>
                        </div>
                        <div class="phone-item">
                            <div class="p">Support: {{ $system['contact_technical_phone'] }}</div>
                            <div class="worktime">Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
                <div class="social">
                    <div class="uk-flex uk-flex-middle">
                        <div class="span">Follow us:</div>
                        <div class="social-list">
                            @php
                                $social = ['facebook', 'twitter', 'youtube'];
                            @endphp
                            <div class="uk-flex uk-flex-middle">
                                @foreach ($social as $key => $val)
                                    <a target="_blank" href="{{ $system['social_' . $val] }}" class=""><i
                                            class="fa fa-{{ $val }}"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
