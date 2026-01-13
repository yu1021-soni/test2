@if ($openRatingModal)
<div class="modal-overlay"></div>

    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal__content">
        <div class="modal__header">
            <a class="modal__close"
                href="{{ route('transaction.show', request()->route()->parameters() + request()->except('modal')) }}"
                aria-label="閉じる">×</a>
        </div>

        <h2 id="modalTitle">取引が完了しました</h2>

        {{-- ★評価フォーム --}}
        <form method="POST" action="{{ route('transaction.evaluation', ['transaction' => $transaction->id]) }}">
        @csrf
            <div class="stars">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
            </div>

        <button type="submit">送信</button>
      </form>
    </div>
  </div>
@endif