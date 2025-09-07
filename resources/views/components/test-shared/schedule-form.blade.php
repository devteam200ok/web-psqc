@if ($showScheduleForm)
    <div class="card bg-light mb-3">
        <div class="card-body">
            <h6 class="card-title">Schedule Test</h6>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" wire:model="scheduleDate"
                        class="form-control @error('scheduleDate') is-invalid @enderror" min="{{ date('Y-m-d') }}">
                    @error('scheduleDate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hour</label>
                    <select wire:model="scheduleHour" class="form-select @error('scheduleHour') is-invalid @enderror">
                        <option value="">Select Hour</option>
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ sprintf('%02d', $i) }}">
                                {{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                    @error('scheduleHour')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Minute</label>
                    <select wire:model="scheduleMinute"
                        class="form-select @error('scheduleMinute') is-invalid @enderror">
                        <option value="">Select Minute</option>
                        @for ($i = 0; $i < 60; $i += 5)
                            <option value="{{ sprintf('%02d', $i) }}">
                                {{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                    @error('scheduleMinute')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button wire:click="scheduleTest" class="btn btn-primary">Schedule</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
