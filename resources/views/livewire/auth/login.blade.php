<div class="auth-card">
    <div class="auth-logo">
        <span class="icon">💎</span>
        <h1>Five Finger</h1>
        <p>Sign in to your shop account</p>
    </div>

    @if($errors->has('email'))
        <div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#F87171;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form wire:submit="login">
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" wire:model="email" class="form-control" placeholder="admin@jewel.com" id="login-email" autocomplete="email">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" wire:model="password" class="form-control" placeholder="••••••••" id="login-password" autocomplete="current-password">
        </div>
        <button type="submit" class="btn-gold" id="btn-login" wire:loading.attr="disabled">
            <span wire:loading.remove>Sign In →</span>
            <span wire:loading>Signing in...</span>
        </button>
    </form>
</div>
