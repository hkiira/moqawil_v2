<?php 
$this->layout = 'login';
?>
<style>
    .login-input {
        border: 2px solid #e9ecef !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        transition: all 0.3s ease;
        height: auto !important;
    }
    .login-input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }
    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 12px 20px !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        width: 100% !important;
        transition: all 0.3s ease !important;
        margin-top: 10px;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4) !important;
    }
    .btn-login:active {
        transform: translateY(0);
    }
</style>

<?= $this->Form->create(null, ['class' => 'login-form']) ?>

    <div class="form-group">
        <label class="form-label">Nom d'utilisateur</label>
        <?= $this->Form->control('username',[
            'class'=>'form-control login-input',
            'label'=>false,
            'placeholder' => 'Entrez votre nom d\'utilisateur',
            'type' => 'text'
        ]) ?>
    </div>

    <div class="form-group">
        <label class="form-label">Mot de passe</label>
        <?= $this->Form->control('password',[
            'class'=>'form-control login-input',
            'label'=>false,
            'placeholder' => 'Entrez votre mot de passe'
        ]) ?>
    </div>

    <div class="form-group text-right mt-2 mb-4">
        <a href="#" class="text-muted small" style="text-decoration: none;">Mot de passe oublié?</a>
    </div>

    <button type="submit" class="btn btn-login text-white font-weight-bold">Connexion</button>

<?= $this->Form->end() ?>
