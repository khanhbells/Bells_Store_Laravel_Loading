@php
    $totalReviews = $model->reviews()->count();
    $totalRate = number_format($model->reviews()->avg('score'), 1);
    $starPercent = $totalReviews == 0 ? '0' : ($totalRate / 5) * 100;

@endphp
<div class="review-container">
    <div class="panel-head">
        <h2 class="review-heading">Đánh giá sản phẩm</h2>
        <div class="review-statistic">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                <div class="uk-width-large-1-3">
                    <div class="review-averate review-item">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent }}%;"></div>
                        </div>
                        <div class="total-rate">
                            {{ $totalReviews }} đánh giá
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="progress-block review-item">
                        @for ($i = 5; $i >= 1; $i--)
                            @php
                                $countStar = $model->reviews()->where('score', $i)->count();
                                $starPercent = $countStar > 0 ? ($countStar / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="progress-item">
                                <div class="uk-flex uk-flex-middle">
                                    <span class="text">{{ $i }}</span>
                                    <i class="fa fa-star"></i>
                                    <div class="uk-progress">
                                        <div class="uk-progress-bar" style="width: {{ $starPercent }}%;"></div>
                                    </div>
                                    <span class="text">{{ $countStar }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="review-action review-item">
                        <div class="text">Bạn đã dùng sản phẩm này?</div>
                        <button class="btn btn-review" data-uk-modal="{target:'#review'}">Đánh giá</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        {{-- <div class="review-filter">
            <div class="uk-flex uk-flex-middle">
                <span class="filter-text">Lọc xem theo: </span>
                <div class="filter-item">
                    <span>Đã mua hàng</span>
                    <span>5 sao</span>
                    <span>4 sao</span>
                    <span>3 sao</span>
                    <span>2 sao</span>
                    <span>1 sao</span>
                </div>
            </div>
        </div> --}}
        <div class="review-wrapper">
            @if (!is_null($product->reviews))
                @foreach ($product->reviews as $review)
                    @php
                        $avatar = getReviewName($review->fullname);
                        $fullname = $review->fullname;
                        $email = $review->email;
                        $phone = $review->phone;
                        $description = $review->description;
                        $rating = generateStar($review->score);
                        $created_at = convertDateTime($review->created_at);
                    @endphp
                    <div class="review-block-item">
                        <div class="review-general uk-clearfix">
                            <div class="review-avatar">
                                <span class="shae">{{ $avatar }}</span>
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name uk-flex uk-flex-middle">
                                        <span>{{ $fullname }}</span>
                                        {{-- <span class="review-buy">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            Đã mua hàng tại {{ $system['homepage_company'] }}
                                        </span> --}}
                                    </div>
                                    {!! $rating !!}
                                    <div class="desciption">
                                        {{ $description }}
                                    </div>
                                    <div class="review-toolbox">
                                        <div class="uk-flex uk-flex-middle mt10">
                                            <div class="created_at">Ngày {{ $created_at }}</div>
                                            {{-- <div class="review-reply" data-uk-modal="{target:'#review'}">
                                                Trả lời
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="review-block-item uk-clearfix reply-block">
                            <div class="review-avatar">
                                <span class="shae">QA</span>
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name uk-flex uk-flex-middle">
                                        <span>Admin</span>
                                        <span class="review-buy">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            Hệ thống
                                        </span>
                                    </div>
                                    <div class="review-star">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="desciption">
                                        Cũng như quạt sưởi, Đèn sưởi Kangaroo KG247V cũng là loại sản phẩm thiết yếu cho
                                        các
                                        gia đình trong mỗi mùa đông, đặc biệt các gia đình có trẻ nhỏ, người lớn tuổi có
                                        sức
                                        khỏe nhạy cảm với sự thay đổi thời tiết. Với các ưu điểm thiết kế nhỏ gọn, dễ
                                        lắp
                                        đặt, công tắc riêng biệt và đa dạng công suất, chiếc đèn sưởi này hứa hẹn sẽ đáp
                                        ứng
                                        được mọi nhu cầu sử dụng trong ngôi nhà của bạn.
                                    </div>
                                    <div class="review-toolbox">
                                        <div class="uk-flex uk-flex-middle mt10">
                                            <div class="created_at">Ngày 11/11/2024</div>
                                            <div class="review-reply">
                                                Trả lời
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<div id="review" class="uk-modal">
    <div class="uk-modal-dialog">
        <a href="" class="uk-modal-close uk-close"></a>
        <div class="review-popup-wrapper">
            <div class="panel-head">Đánh giá sản phẩm</div>
            <div class="panel-body">
                <div class="product-preview">
                    <span class="image img-scaledown"><img src="{{ asset($model->image) }}"
                            alt="{{ $model->name }}"></span>
                    <div class="product-title">
                        {{ $model->name }}
                    </div>
                    <div class="popup-rating uk-clearfix uk-text-center">
                        <div class="rate uk-clearfix">
                            <input type="radio" id="star5" name="rate" class="rate" value="5" />
                            <label for="star5" title="Tuyệt vời">5 stars</label>
                            <input type="radio" id="star4" name="rate" class="rate" value="4" />
                            <label for="star4" title="Hài lòng">4 stars</label>
                            <input type="radio" id="star3" name="rate" class="rate" value="3" />
                            <label for="star3" title="Bình thường">3 stars</label>
                            <input type="radio" id="star2" name="rate" class="rate" value="2" />
                            <label for="star2" title="Tạm được">2 stars</label>
                            <input type="radio" id="star1" name="rate" class="rate" value="1" />
                            <label for="star1" title="Không thích">1 star</label>
                        </div>
                        <div class="rate-text">
                        </div>
                    </div>
                    <div class="review-form">
                        <div class="uk-form form">
                            <div class="form-row">
                                <textarea name="" id="" class="review-textarea"
                                    placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" value="Nam" class="gender"
                                            id="male">
                                        <label for="">Nam</label>
                                    </div>
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" value="Nữ" class="gender"
                                            id="female">
                                        <label for="">Nữ</label>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-grid uk-grid-medium">
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="fullname" value="" class="review-text"
                                            placeholder="Nhập vào họ tên">
                                    </div>
                                </div>
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="phone" value="" class="review-text"
                                            placeholder="Nhập vào số điện thoại">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <input type="text" name="email" value="" class="review-text"
                                    placeholder="Nhập vào email">
                            </div>
                            <div class="uk-text-center">
                                <button type="submit" value="send" name="create" class="btn-send-review">Hoàn
                                    tất</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $model->id }}">
<input type="hidden" class="review_parent_id" value="0">
