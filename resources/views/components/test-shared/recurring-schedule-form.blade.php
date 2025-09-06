@if ($showRecurringForm)
    <div class="card bg-light mb-3">
        <div class="card-body">
            <h6 class="card-title">스케쥴 등록</h6>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">시작일</label>
                    <input type="date" wire:model="recurringStartDate"
                        class="form-control @error('recurringStartDate') is-invalid @enderror" min="{{ date('Y-m-d') }}">
                    @error('recurringStartDate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">종료일</label>
                    <input type="date" wire:model="recurringEndDate"
                        class="form-control @error('recurringEndDate') is-invalid @enderror" min="{{ date('Y-m-d') }}">
                    @error('recurringEndDate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">시</label>
                    <select wire:model="recurringHour" class="form-select @error('recurringHour') is-invalid @enderror">
                        <option value="">시 선택</option>
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ sprintf('%02d', $i) }}">
                                {{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                    @error('recurringHour')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">분</label>
                    <select wire:model="recurringMinute"
                        class="form-select @error('recurringMinute') is-invalid @enderror">
                        <option value="">분 선택</option>
                        @for ($i = 0; $i < 60; $i += 5)
                            <option value="{{ sprintf('%02d', $i) }}">
                                {{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                    @error('recurringMinute')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button wire:click="createRecurringSchedule" class="btn btn-primary">등록</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
