<?php

return array(
    'ru' => array(

        'title_change_password_account' => 'Смена пароля для аккаунта: ',

        "lang_input_password_old" => "Текуший пароль",
        "lang_input_password_new" => "Новый пароль",
        "lang_input_password_new_confirm" => "Пароль еще раз",
        "lang_input_password_pin" => "Введите PIN-CODE",
        "lang_input_send" => "Способ доставки",
        "lang_button_change" => "Сменить пароль",


        "lang_tab_title_settings" => "Настройки",
        "lang_tab_title_settings_desc" => "Настройки мастер аккаунта",
        "lang_tab_title_manager" => "Управление МА",
        "lang_tab_title_manager_desc" => "Управление мастер аккаунтами",
        "lang_tab_title_account_hide" => "Скрытые аккаунты",
        "lang_tab_title_account_hide_desc" => "Управление скрытыми аккаунтами",
        "lang_tab_title_logs" => "Логи действий",
        "lang_tab_title_invoice" => "Платежи",

        "lang_tab_title_logs_desc" => "Логи последних операций",
        "lang_tab_logs_th_action" => "Действие",
        "lang_tab_logs_th_date" => "Дата",
        "lang_tab_invoice_th_payment" => "Платежный шлюз",
        "lang_tab_invoice_th_sum" => "Сумма",

        'title_forgot_password_account' => 'Восстановление пароля для аккаунта: ',
        "lang_button_forgot" => " Восстановить пароль",

        'ajax_empty_account' => 'Не переданан аккаунт, обновите страницу!',

		/**
		 * Вкладка: безопасность
		 */
		'security_tab_title' => 'Безопасность',
		'two_factor_auth_title' => 'Двухфакторная аутентификация',
		'two_factor_enable_button' => 'Включить 2FA',

		'change_password_title' => 'Пароль мастер-аккаунта',
		'change_password_button' => 'Изменить пароль',

		'notifications_title' => 'Настройки уведомлений',
		'notofications_send_to' => 'Отправлять уведомления',
		'notifications_send_to_email' => 'На E-Mail',
		'notifications_send_to_phone' => 'На телефон',
		'notifications_send_to_telegram' => 'в Telegram',
		'notifications_on_signin_from_new_devices' => 'При входе с новых устройств',
		'notifications_on_balance_debit' => 'При трате баланса',
		'notifications_on_password_change' => 'При смене пароля',
		'notifications_save_button' => 'Сохранить',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => 'телефон',
		],

		'two_factor_auth_method_enable_popup_info' => 'Выберите метод двухфакторной аутентификации:',
		'two_factor_auth_method_enable_popup_required_info' => 'Для получения полного доступа к возможностям личного кабинета необходимо включить двухфакторную аутентификацию.',
		'two_factor_auth_method_enable_not_available_info' => 'Для включения двухфакторной аутентификации через [[ methods[selectedMethod].label ]], сначала необходимо',
		'two_factor_auth_method_enable_not_available_link' => 'подтвердить его',
		'two_factor_auth_method_enable_send_code_to' => 'Отправить код на',
		'two_factor_auth_method_enable_retry_send_after' => 'Повторная отправка кода через',
		'two_factor_auth_method_enable_retry_send_button' => 'Отправить код повторно',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => 'Код отправлен',
			'EMAIL_NOT_VERIFIED' => 'Необходимо <a href="' . set_url('/panel/settings') . '">подтвердить E-Mail</a>',
			'PHONE_NOT_VERIFIED' => 'Необходимо <a href="' . set_url('/panel/settings') . '">подтвердить телефон</a>',
			'ACTIVATION_NOT_STARTED' => 'Не удалось начать процесс активации двухфакторной аутентификации, попробуйте позже.',
			'ACTIVATION_NOT_CONFIRMED' => 'Не удалось подтвердить активацию двухфакторной аутентификации, попробуйте позже.',
			'METHOD_ALREADY_ENABLED' => 'Метод уже активирован.',
			'VERIFICATION_CODE_ALREADY_SENT' => 'Код уже был отправлен.',
			'UNKNOWN_ERROR' => 'Неизвестная ошибка, попробуйте позже.',
			'INVALID_VERIFICATION_CODE' => 'Неверный код подтверждения.',
			'ACTIVATION_PROCESS_NOT_FOUND' => 'Процесс подтверждения активации истек. Отправьте код еще раз.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => 'Двухфакторная аутентификация не включена.',
			'TWO_FACTOR_DISABLED' => 'Двухфакторная аутентификация отключена.',
			'VERIFICATION_PROCESS_NOT_FOUND' => 'Процесс подтверждения деактивации истек. Отправьте код еще раз.',
			'METHOD_NOT_ENABLED' => 'Выбранный метод двухфакторной аутентификации не включен.',
			'INVALID_VERIFICATION_CODE' => 'Неверный код подтверждения.',
			'UNKNOWN_ERROR' => 'Неизвестная ошибка, попробуйте позже.',
		],

		'two_factor_auth_enabled' => '2FA включен',
		'two_factor_auth_add_method' => 'Добавить метод проверки',
		'two_factor_auth_disable' => 'Отключить 2FA',
		'two_factor_auth_method_empty' => 'Метод не передан',
		'two_factor_auth_code_empty' => 'Код не передан',

    ),

    'en' => array(

		'title_change_password_account' => 'Change Master Account Password:',

		"lang_input_password_old" => "Current password",
		"lang_input_password_new" => "New password",
		"lang_input_password_new_confirm" => "Re-enter password",
		"lang_input_password_pin" => "Enter PIN-Code",
		"lang_input_send" => "Delivery method",
		"lang_button_change" => "Change password",


		"lang_tab_title_settings" => "Settings",
		"lang_tab_title_settings_desc" => "Master Account settings",
		"lang_tab_title_manager" => "Master Account Management",
		"lang_tab_title_manager_desc" => "Manage Master Accounts",
		"lang_tab_title_logs" => "Action logs",
        "lang_tab_title_invoice" => "Invoices",
        "lang_tab_title_account_hide" => "Hide Master Account",
        "lang_tab_title_account_hide_desc" => "Hidden Master Account Management",

		"lang_tab_title_logs_desc" => "Recent activity logs",
		"lang_tab_logs_th_action" => "Action",
		"lang_tab_logs_th_date" => "Date",
        "lang_tab_invoice_th_payment" => "Payment method",
        "lang_tab_invoice_th_sum" => "Amount",





		'title_forgot_password_account' => 'Master Account Password Recovery:',
		"lang_button_forgot" => "Reset password",

		// AJAX
		'ajax_empty_account' => 'The account cannot be found! Please refresh the page!',

		// Security Tab
		'security_tab_title' => 'Security',
		'two_factor_auth_title' => 'Two-Factor Authentication',
		'two_factor_enable_button' => 'Enable 2FA',

		'change_password_title' => 'Master Account Password',
		'change_password_button' => 'Change Password',

		'notifications_title' => 'Notification Settings',
		'notofications_send_to' => 'Send notifications to',
		'notifications_send_to_email' => 'To E-Mail',
		'notifications_send_to_phone' => 'To Phone',
		'notifications_send_to_telegram' => 'To Telegram',
		'notifications_on_signin_from_new_devices' => 'When signing in from new devices',
		'notifications_on_balance_debit' => 'When balance is spent',
		'notifications_on_password_change' => 'When password is changed',
		'notifications_save_button' => 'Save',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => 'Phone',
		],

		'two_factor_auth_method_enable_popup_info' => 'Select the two-factor authentication:',
		'two_factor_auth_method_enable_popup_required_info' => 'To gain full access to all features of your account, you must enable two-factor authentication.',
		'two_factor_auth_method_enable_not_available_info' => 'To enable two-factor authentication via [[ methods[selectedMethod].label ]], first you need to',
		'two_factor_auth_method_enable_not_available_link' => 'confirm it',
		'two_factor_auth_method_enable_send_code_to' => 'Send code to',
		'two_factor_auth_method_enable_retry_send_after' => 'Resend code after',
		'two_factor_auth_method_enable_retry_send_button' => 'Resend code',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => 'Code sent',
			'EMAIL_NOT_VERIFIED' => 'You must <a href="' . set_url('/panel/settings') . '">verify E-Mail</a>',
			'PHONE_NOT_VERIFIED' => 'You must <a href="' . set_url('/panel/settings') . '">verify phone</a>',
			'ACTIVATION_NOT_STARTED' => 'Failed to start two-factor authentication activation process, try again later.',
			'ACTIVATION_NOT_CONFIRMED' => 'Failed to confirm two-factor authentication activation, try again later.',
			'METHOD_ALREADY_ENABLED' => 'Method already enabled.',
			'VERIFICATION_CODE_ALREADY_SENT' => 'Code has already been sent.',
			'UNKNOWN_ERROR' => 'Unknown error, try again later.',
			'INVALID_VERIFICATION_CODE' => 'Invalid verification code.',
			'ACTIVATION_PROCESS_NOT_FOUND' => 'The activation verification process has expired. Please request the code again.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => 'Two-factor authentication is not enabled.',
			'TWO_FACTOR_DISABLED' => 'Two-factor authentication is disabled.',
			'VERIFICATION_PROCESS_NOT_FOUND' => 'The deactivation verification process has expired. Please request the code again.',
			'METHOD_NOT_ENABLED' => 'Selected two-factor authentication method is not enabled.',
			'INVALID_VERIFICATION_CODE' => 'Invalid verification code.',
			'UNKNOWN_ERROR' => 'Unknown error, try again later.',
		],

		'two_factor_auth_enabled' => '2FA enabled',
		'two_factor_auth_add_method' => 'Add verification method',
		'two_factor_auth_disable' => 'Disable 2FA',
		'two_factor_auth_method_empty' => 'Method not provided',
		'two_factor_auth_code_empty' => 'Code not provided',

    ),

    'gr' => array(

		'title_change_password_account' => 'Αλλαγή Password Master Account:',

		"lang_input_password_old" => "Τρέχων password",
		"lang_input_password_new" => "Νέο password",
		"lang_input_password_new_confirm" => "Επαλήθευση password",
		"lang_input_password_pin" => "Εισάγετε PIN-Code",
		"lang_input_send" => "Μέθοδος αποστολής",
		"lang_button_change" => "Αλλαγή password",


		"lang_tab_title_settings" => "Ρυθμίσεις",
		"lang_tab_title_settings_desc" => "Ρυθμίσεις Master Account",
		"lang_tab_title_manager" => "Διαχείρηση Master Account",
		"lang_tab_title_manager_desc" => "Διαχειριστείτε τα Master Accounts σας",
		"lang_tab_title_logs" => "Αρχείο ενεργειών",
        "lang_tab_title_invoice" => "Αποδείξεις",
        "lang_tab_title_account_hide" => "Απόκρυψη Master Account",
        "lang_tab_title_account_hide_desc" => "Διαχείρηση Αποκρυμμένων Master Accounts",

		"lang_tab_title_logs_desc" => "Αρχείο πρόσφατων ενεργειών",
		"lang_tab_logs_th_action" => "Ενέργεια",
		"lang_tab_logs_th_date" => "Ημερομηνία",
        "lang_tab_invoice_th_payment" => "Τρόπος πληρωμής",
        "lang_tab_invoice_th_sum" => "Σύνολο",





		'title_forgot_password_account' => 'Ανάκτηση Password Master Account:',
		"lang_button_forgot" => "Ανάκτηση password",

		// AJAX
		'ajax_empty_account' => 'Τo account δεν βρέθηκε! Ανανεώστε τη σελίδα!',

		// Security Tab
		'security_tab_title' => 'Ασφάλεια',
		'two_factor_auth_title' => 'Διακρίβωση Δύο Παραγόντων',
		'two_factor_enable_button' => 'Ενεργοποίηση 2FA',

		'change_password_title' => 'Password Master Account',
		'change_password_button' => 'Αλλαγή password',

		'notifications_title' => 'Ρυθμίσεις Ειδοποιήσεων',
		'notofications_send_to' => 'Αποστολή ειδοποιήσεων σε',
		'notifications_send_to_email' => 'Στο E-Mail',
		'notifications_send_to_phone' => 'Στο Τηλέφωνο',
		'notifications_send_to_telegram' => 'Στο Telegram',
		'notifications_on_signin_from_new_devices' => 'Κατά την σύνδεση από νέες συσκευές',
		'notifications_on_balance_debit' => 'Όταν ξοδεύεται το υπόλοιπο',
		'notifications_on_password_change' => 'Όταν αλλάζει ο κωδικός πρόσβασης',
		'notifications_save_button' => 'Αποθήκευση',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => 'Τηλέφωνο',
		],

		'two_factor_auth_method_enable_popup_info' => 'Επιλέξτε τη μέθοδο ελέγχου ταυτότητας δύο παραγόντων:',
		'two_factor_auth_method_enable_popup_required_info' => 'Για να αποκτήσετε πλήρη πρόσβαση στις δυνατότητες του προσωπικού σας λογαριασμού, πρέπει να ενεργοποιήσετε τον έλεγχο ταυτότητας δύο παραγόντων.',
		'two_factor_auth_method_enable_not_available_info' => 'Για να ενεργοποιήσετε τη διακρίβωση δύο παραγόντων μέσω [[ methods[selectedMethod].label ]], πρώτα πρέπει να',
		'two_factor_auth_method_enable_not_available_link' => 'επιβεβαιώσετε το',
		'two_factor_auth_method_enable_send_code_to' => 'Αποστολή κωδικού σε',
		'two_factor_auth_method_enable_retry_send_after' => 'Επαναποστολή κωδικού μετά από',
		'two_factor_auth_method_enable_retry_send_button' => 'Αποστολή κωδικού ξανά',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => 'Κωδικός αποστολής',
			'EMAIL_NOT_VERIFIED' => 'Πρέπει να <a href="' . set_url('/panel/settings') . '">επιβεβαιώσετε το E-Mail</a>',
			'PHONE_NOT_VERIFIED' => 'Πρέπει να <a href="' . set_url('/panel/settings') . '">επιβεβαιώσετε το τηλέφωνο</a>',
			'ACTIVATION_NOT_STARTED' => 'Δεν ήταν δυνατή η έναρξη της διαδικασίας ενεργοποίησης διακρίβωσης δύο παραγόντων, δοκιμάστε ξανά αργότερα.',
			'ACTIVATION_NOT_CONFIRMED' => 'Δεν ήταν δυνατή η επιβεβαίωση της ενεργοποίησης διακρίβωσης δύο παραγόντων, δοκιμάστε ξανά αργότερα.',
			'METHOD_ALREADY_ENABLED' => 'Η μέθοδος είναι ήδη ενεργοποιημένη.',
			'VERIFICATION_CODE_ALREADY_SENT' => 'Ο κωδικός έχει ήδη αποσταλεί.',
			'UNKNOWN_ERROR' => 'Άγνωστο σφάλμα, δοκιμάστε ξανά αργότερα.',
			'INVALID_VERIFICATION_CODE' => 'Μη έγκυρος κωδικός επιβεβαίωσης.',
			'ACTIVATION_PROCESS_NOT_FOUND' => 'Η διαδικασία επιβεβαίωσης ενεργοποίησης έχει λήξει. Παρακαλούμε ζητήστε το κωδικό ξανά.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => 'Η διακρίβωση δύο παραγόντων δεν είναι ενεργοποιημένη.',
			'TWO_FACTOR_DISABLED' => 'Η διακρίβωση δύο παραγόντων είναι απενεργοποιημένη.',
			'VERIFICATION_PROCESS_NOT_FOUND' => 'Η διαδικασία επιβεβαίωσης απενεργοποίησης έχει λήξει. Παρακαλούμε ζητήστε το κωδικό ξανά.',
			'METHOD_NOT_ENABLED' => 'Η επιλεγμένη μέθοδος διακρίβωσης δύο παραγόντων δεν είναι ενεργοποιημένη.',
			'INVALID_VERIFICATION_CODE' => 'Μη έγκυρος κωδικός επιβεβαίωσης.',
			'UNKNOWN_ERROR' => 'Άγνωστο σφάλμα, δοκιμάστε ξανά αργότερα.',
		],

		'two_factor_auth_enabled' => '2FA ενεργοποιημένη',
		'two_factor_auth_add_method' => 'Προσθήκη μεθόδου επιβεβαίωσης',
		'two_factor_auth_disable' => 'Απενεργοποίηση 2FA',
		'two_factor_auth_method_empty' => 'Μέθοδος δεν παρέχεται',
		'two_factor_auth_code_empty' => 'Κωδικός δεν παρέχεται',

	),

	'es' => array(

		'title_change_password_account' => 'Cambiar Contraseña de la Cuenta Maestra:',

		"lang_input_password_old" => "Contraseña actual",
		"lang_input_password_new" => "Nueva contraseña",
		"lang_input_password_new_confirm" => "Re-ingresa contraseña",
		"lang_input_password_pin" => "Ingresa Código-PIN",
		"lang_input_send" => "Método de entrega",
		"lang_button_change" => "Cambiar contraseña",


		"lang_tab_title_settings" => "Ajustes",
		"lang_tab_title_settings_desc" => "Ajustes de Cuenta Maestra",
		"lang_tab_title_manager" => "Administración de Cuenta Maestra",
		"lang_tab_title_manager_desc" => "Administrar Cuentas Maestras",
		"lang_tab_title_logs" => "Registro de actividades",
        "lang_tab_title_invoice" => "Facturas",
        "lang_tab_title_account_hide" => "Ocultar Cuenta Maestra",
        "lang_tab_title_account_hide_desc" => "Administración de Cuenta Maestra oculta",

		"lang_tab_title_logs_desc" => "Registro de actividades recientes",
		"lang_tab_logs_th_action" => "Actividad",
		"lang_tab_logs_th_date" => "Fecha",
        "lang_tab_invoice_th_payment" => "Método de pago",
        "lang_tab_invoice_th_sum" => "Cantidad",





		'title_forgot_password_account' => 'Recuperación de Contraseña de la Cuenta Maestra:',
		"lang_button_forgot" => "Restablecer contraseña",

		// AJAX
		'ajax_empty_account' => 'La cuenta no puede ser encontrada! Por favor actualiza la página!',

		// Security Tab
		'security_tab_title' => 'Seguridad',
		'two_factor_auth_title' => 'Autenticación de Dos Factores',
		'two_factor_enable_button' => 'Habilitar 2FA',

		'change_password_title' => 'Contraseña de Cuenta Maestra',
		'change_password_button' => 'Cambiar contraseña',

		'notifications_title' => 'Configuración de Notificaciones',
		'notofications_send_to' => 'Enviar notificaciones a',
		'notifications_send_to_email' => 'Al E-Mail',
		'notifications_send_to_phone' => 'Al Teléfono',
		'notifications_send_to_telegram' => 'A Telegram',
		'notifications_on_signin_from_new_devices' => 'Cuando inicia sesión desde nuevos dispositivos',
		'notifications_on_balance_debit' => 'Cuando se gasta el saldo',
		'notifications_on_password_change' => 'Cuando cambia la contraseña',
		'notifications_save_button' => 'Guardar',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => 'Teléfono',
		],

		'two_factor_auth_method_enable_popup_info' => 'Seleccione el método de autenticación de dos factores:',
		'two_factor_auth_method_enable_popup_required_info' => 'Para obtener acceso completo a todas las funciones de su cuenta personal, debe habilitar la autenticación de dos factores.',
		'two_factor_auth_method_enable_not_available_info' => 'Para habilitar la autenticación de dos factores mediante [[ methods[selectedMethod].label ]], primero debes',
		'two_factor_auth_method_enable_not_available_link' => 'confirmar',
		'two_factor_auth_method_enable_send_code_to' => 'Enviar código a',
		'two_factor_auth_method_enable_retry_send_after' => 'Reenviar código después de',
		'two_factor_auth_method_enable_retry_send_button' => 'Reenviar código',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => 'Código enviado',
			'EMAIL_NOT_VERIFIED' => 'Debes <a href="' . set_url('/panel/settings') . '">verificar el E-Mail</a>',
			'PHONE_NOT_VERIFIED' => 'Debes <a href="' . set_url('/panel/settings') . '">verificar el teléfono</a>',
			'ACTIVATION_NOT_STARTED' => 'Error al iniciar el proceso de activación de autenticación de dos factores, intenta más tarde.',
			'ACTIVATION_NOT_CONFIRMED' => 'Error al confirmar la activación de autenticación de dos factores, intenta más tarde.',
			'METHOD_ALREADY_ENABLED' => 'El método ya está habilitado.',
			'VERIFICATION_CODE_ALREADY_SENT' => 'El código ya ha sido enviado.',
			'UNKNOWN_ERROR' => 'Error desconocido, intenta más tarde.',
			'INVALID_VERIFICATION_CODE' => 'Código de verificación inválido.',
			'ACTIVATION_PROCESS_NOT_FOUND' => 'El proceso de verificación de activación ha expirado. Por favor, solicita el código nuevamente.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => 'La autenticación de dos factores no está habilitada.',
			'TWO_FACTOR_DISABLED' => 'La autenticación de dos factores está deshabilitada.',
			'VERIFICATION_PROCESS_NOT_FOUND' => 'El proceso de verificación de desactivación ha expirado. Por favor, solicita el código nuevamente.',
			'METHOD_NOT_ENABLED' => 'El método de autenticación de dos factores seleccionado no está habilitado.',
			'INVALID_VERIFICATION_CODE' => 'Código de verificación inválido.',
			'UNKNOWN_ERROR' => 'Error desconocido, intenta más tarde.',
		],

		'two_factor_auth_enabled' => '2FA habilitado',
		'two_factor_auth_add_method' => 'Agregar método de verificación',
		'two_factor_auth_disable' => 'Deshabilitar 2FA',
		'two_factor_auth_method_empty' => 'Método no proporcionado',
		'two_factor_auth_code_empty' => 'Código no proporcionado',

    ),

    'pt' => array(

		'title_change_password_account' => 'Alterar senha da conta mestre:',

		"lang_input_password_old" => "Senha atual",
		"lang_input_password_new" => "Nova senha",
		"lang_input_password_new_confirm" => "Digite novamente a senha",
		"lang_input_password_pin" => "Digite o código PIN",
		"lang_input_send" => "Método de Entrega",
		"lang_button_change" => "Alterar a senha",

		"lang_tab_title_settings" => "Configurações",
		"lang_tab_title_settings_desc" => "Configurações da conta mestre",
		"lang_tab_title_manager" => "Gerenciamento de conta mestre",
		"lang_tab_title_manager_desc" => "Gerenciar contas mestras",
		"lang_tab_title_logs" => "Registros de ação",
        "lang_tab_title_invoice" => "Faturas",
        "lang_tab_title_account_hide" => "Contas ocultas",
        "lang_tab_title_account_hide_desc" => "Gerenciamento de contas ocultas",

		"lang_tab_title_logs_desc" => "Registro de atividades recentes",
		"lang_tab_logs_th_action" => "Ação",
		"lang_tab_logs_th_date" => "Data",
        "lang_tab_invoice_th_payment" => "Método de pagamento",
        "lang_tab_invoice_th_sum" => "Quantidade",

		'title_forgot_password_account' => 'Recuperação de senha da conta mestre:',
		"lang_button_forgot" => "Redefinir senha",

		// AJAX
		'ajax_empty_account' => 'A conta não pode ser encontrada! Por favor, atualize a página!',

		// Security Tab
		'security_tab_title' => 'Segurança',
		'two_factor_auth_title' => 'Autenticação de Dois Fatores',
		'two_factor_enable_button' => 'Habilitar 2FA',

		'change_password_title' => 'Senha da Conta Mestre',
		'change_password_button' => 'Alterar a senha',

		'notifications_title' => 'Configurações de Notificação',
		'notofications_send_to' => 'Enviar notificações para',
		'notifications_send_to_email' => 'Para E-Mail',
		'notifications_send_to_phone' => 'Para Telefone',
		'notifications_send_to_telegram' => 'Para Telegram',
		'notifications_on_signin_from_new_devices' => 'Ao fazer login em novos dispositivos',
		'notifications_on_balance_debit' => 'Quando o saldo é gasto',
		'notifications_on_password_change' => 'Quando a senha é alterada',
		'notifications_save_button' => 'Salvar',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => 'Telefone',
		],

		'two_factor_auth_method_enable_popup_info' => 'Selecione o método de autenticação de dois fatores:',
		'two_factor_auth_method_enable_popup_required_info' => 'Para obter acesso completo a todos os recursos da sua conta, é necessário ativar a autenticação de dois fatores.',
		'two_factor_auth_method_enable_not_available_info' => 'Para ativar a autenticação de dois fatores via [[ methods[selectedMethod].label ]], primeiro você precisa',
		'two_factor_auth_method_enable_not_available_link' => 'confirmá-lo',
		'two_factor_auth_method_enable_send_code_to' => 'Enviar código para',
		'two_factor_auth_method_enable_retry_send_after' => 'Reenviar código após',
		'two_factor_auth_method_enable_retry_send_button' => 'Reenviar código',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => 'Código enviado',
			'EMAIL_NOT_VERIFIED' => 'Você deve <a href="' . set_url('/panel/settings') . '">verificar E-Mail</a>',
			'PHONE_NOT_VERIFIED' => 'Você deve <a href="' . set_url('/panel/settings') . '">verificar telefone</a>',
			'ACTIVATION_NOT_STARTED' => 'Falha ao iniciar o processo de ativação de autenticação de dois fatores, tente novamente mais tarde.',
			'ACTIVATION_NOT_CONFIRMED' => 'Falha ao confirmar a ativação de autenticação de dois fatores, tente novamente mais tarde.',
			'METHOD_ALREADY_ENABLED' => 'Método já está habilitado.',
			'VERIFICATION_CODE_ALREADY_SENT' => 'O código já foi enviado.',
			'UNKNOWN_ERROR' => 'Erro desconhecido, tente novamente mais tarde.',
			'INVALID_VERIFICATION_CODE' => 'Código de verificação inválido.',
			'ACTIVATION_PROCESS_NOT_FOUND' => 'O processo de verificação de ativação expirou. Por favor, solicite o código novamente.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => 'Autenticação de dois fatores não está habilitada.',
			'TWO_FACTOR_DISABLED' => 'Autenticação de dois fatores está desabilitada.',
			'VERIFICATION_PROCESS_NOT_FOUND' => 'O processo de verificação de desativação expirou. Por favor, solicite o código novamente.',
			'METHOD_NOT_ENABLED' => 'Método de autenticação de dois fatores selecionado não está habilitado.',
			'INVALID_VERIFICATION_CODE' => 'Código de verificação inválido.',
			'UNKNOWN_ERROR' => 'Erro desconhecido, tente novamente mais tarde.',
		],

		'two_factor_auth_enabled' => '2FA habilitado',
		'two_factor_auth_add_method' => 'Adicionar método de verificação',
		'two_factor_auth_disable' => 'Desabilitar 2FA',
		'two_factor_auth_method_empty' => 'Método não fornecido',
		'two_factor_auth_code_empty' => 'Código não fornecido',

    ),

	'cn' => array(

		'title_change_password_account' => '更改主帐户密码:',

		"lang_input_password_old" => "当前密码",
		"lang_input_password_new" => "新密码",
		"lang_input_password_new_confirm" => "重新输入密码",
		"lang_input_password_pin" => "输入PIN码",
		"lang_input_send" => "交货方式",
		"lang_button_change" => "更改密码",


		"lang_tab_title_settings" => "设置",
		"lang_tab_title_settings_desc" => "主帐户设置",
		"lang_tab_title_manager" => "主客户管理",
		"lang_tab_title_manager_desc" => "管理主帐户",
		"lang_tab_title_logs" => "操作日志",
        "lang_tab_title_invoice" => "发票",
        "lang_tab_title_account_hide" => "隐藏主帐户",
        "lang_tab_title_account_hide_desc" => "隐藏主帐户管理",

		"lang_tab_title_logs_desc" => "最近的活动日志",
		"lang_tab_logs_th_action" => "行动",
		"lang_tab_logs_th_date" => "日期",
        "lang_tab_invoice_th_payment" => "支付方式",
        "lang_tab_invoice_th_sum" => "金额",





		'title_forgot_password_account' => '主帐户密码恢复:',
		"lang_button_forgot" => "重置密码",

		// AJAX
		'ajax_empty_account' => '无法找到帐户!请刷新页面!',

		// Security Tab
		'security_tab_title' => '安全',
		'two_factor_auth_title' => '双因素认证',
		'two_factor_enable_button' => '启用2FA',

		'change_password_title' => '主帐户密码',
		'change_password_button' => '更改密码',

		'notifications_title' => '通知设置',
		'notofications_send_to' => '发送通知到',
		'notifications_send_to_email' => '到电子邮件',
		'notifications_send_to_phone' => '到电话',
		'notifications_send_to_telegram' => '到电报',
		'notifications_on_signin_from_new_devices' => '从新设备登录时',
		'notifications_on_balance_debit' => '余额被支出时',
		'notifications_on_password_change' => '密码更改时',
		'notifications_save_button' => '保存',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => '手机',
		],

		'two_factor_auth_method_enable_popup_info' => '请选择双重身份验证方式:',
		'two_factor_auth_method_enable_popup_required_info' => '要获得个人账户的完整功能访问权限，您需要启用双重身份验证。',
		'two_factor_auth_method_enable_not_available_info' => '要通过 [[ methods[selectedMethod].label ]] 启用双因素身份验证，您首先需要',
		'two_factor_auth_method_enable_not_available_link' => '确认它',
		'two_factor_auth_method_enable_send_code_to' => '发送代码到',
		'two_factor_auth_method_enable_retry_send_after' => '重新发送代码在',
		'two_factor_auth_method_enable_retry_send_button' => '重新发送代码',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => '代码已发送',
			'EMAIL_NOT_VERIFIED' => '您必须<a href="' . set_url('/panel/settings') . '">验证电子邮件</a>',
			'PHONE_NOT_VERIFIED' => '您必须<a href="' . set_url('/panel/settings') . '">验证电话</a>',
			'ACTIVATION_NOT_STARTED' => '无法启动双因素身份验证激活过程，请稍后重试。',
			'ACTIVATION_NOT_CONFIRMED' => '无法确认双因素身份验证激活，请稍后重试。',
			'METHOD_ALREADY_ENABLED' => '方法已启用。',
			'VERIFICATION_CODE_ALREADY_SENT' => '代码已发送。',
			'UNKNOWN_ERROR' => '未知错误，请稍后重试。',
			'INVALID_VERIFICATION_CODE' => '验证码无效。',
			'ACTIVATION_PROCESS_NOT_FOUND' => '激活验证过程已过期。请重新请求代码。',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => '双因素身份验证未启用。',
			'TWO_FACTOR_DISABLED' => '双因素身份验证已禁用。',
			'VERIFICATION_PROCESS_NOT_FOUND' => '停用验证过程已过期。请重新请求代码。',
			'METHOD_NOT_ENABLED' => '所选的双因素身份验证方法未启用。',
			'INVALID_VERIFICATION_CODE' => '验证码无效。',
			'UNKNOWN_ERROR' => '未知错误，请稍后重试。',
		],

		'two_factor_auth_enabled' => '2FA 已启用',
		'two_factor_auth_add_method' => '添加验证方法',
		'two_factor_auth_disable' => '禁用 2FA',
		'two_factor_auth_method_empty' => '未提供方法',
		'two_factor_auth_code_empty' => '未提供代码',

    ),

	'ko' => array(

		'title_change_password_account' => '마스터 계정 비밀번호 변:',

		"lang_input_password_old" => "현재 비밀번호",
		"lang_input_password_new" => "새 비밀번호",
		"lang_input_password_new_confirm" => "암호를 다시 입력하십시",
		"lang_input_password_pin" => "입력 PIN-코드",
		"lang_input_send" => "배달 방법",
		"lang_button_change" => "암호 변경",


		"lang_tab_title_settings" => "설정",
		"lang_tab_title_settings_desc" => "마스터 계정 설정",
		"lang_tab_title_manager" => "마스터 계정 관리",
		"lang_tab_title_manager_desc" => "마스터 계정 관리",
		"lang_tab_title_logs" => "동작 기록",
        "lang_tab_title_invoice" => "음성",
        "lang_tab_title_account_hide" => "마스터 계정 숨기기",
        "lang_tab_title_account_hide_desc" => "숨겨진 마스터 계정 관리",

		"lang_tab_title_logs_desc" => "최근 활동 로그",
		"lang_tab_logs_th_action" => "행동",
		"lang_tab_logs_th_date" => "날짜",
        "lang_tab_invoice_th_payment" => "결제 방법",
        "lang_tab_invoice_th_sum" => "금액",





		'title_forgot_password_account' => '마스터 계정 비밀번호 복구:',
		"lang_button_forgot" => "비밀번호 초기",

		// AJAX
		'ajax_empty_account' => '계정을 찾을 수 없습니다! 페이지를 새로 고침하십!',

		// Security Tab
		'security_tab_title' => '보안',
		'two_factor_auth_title' => '이중 인증',
		'two_factor_enable_button' => '2FA 활성화',

		'change_password_title' => '마스터 계정 비밀번호',
		'change_password_button' => '암호 변경',

		'notifications_title' => '알림 설정',
		'notofications_send_to' => '알림 전송 대상',
		'notifications_send_to_email' => '이메일로',
		'notifications_send_to_phone' => '휴대폰으로',
		'notifications_send_to_telegram' => '텔레그램으로',
		'notifications_on_signin_from_new_devices' => '새 장치에서 로그인할 때',
		'notifications_on_balance_debit' => '잔액이 소비될 때',
		'notifications_on_password_change' => '비밀번호가 변경될 때',
		'notifications_save_button' => '저장',

		'two_factor_auth_method_labels' => [
			'email' => 'E-Mail',
			'phone' => '전화',
		],

		'two_factor_auth_method_enable_popup_info' => '2단계 인증 방법을 선택하세요:',
		'two_factor_auth_method_enable_popup_required_info' => '계정의 모든 기능을 이용하려면 2단계 인증을 활성화해야 합니다.',
		'two_factor_auth_method_enable_not_available_info' => '[[ methods[selectedMethod].label ]]를 통해 이중 인증을 활성화하려면 먼저',
		'two_factor_auth_method_enable_not_available_link' => '확인해야 합니다',
		'two_factor_auth_method_enable_send_code_to' => '코드 전송 대상',
		'two_factor_auth_method_enable_retry_send_after' => '코드 재전송 후',
		'two_factor_auth_method_enable_retry_send_button' => '코드 다시 보내기',

		'enable_two_factor_method_status_codes' => [
			'ACTIVATION_STARTED' => '코드가 전송되었습니다',
			'EMAIL_NOT_VERIFIED' => '<a href="' . set_url('/panel/settings') . '">이메일을 확인</a>해야 합니다',
			'PHONE_NOT_VERIFIED' => '<a href="' . set_url('/panel/settings') . '">전화를 확인</a>해야 합니다',
			'ACTIVATION_NOT_STARTED' => '이중 인증 활성화 프로세스를 시작할 수 없습니다. 나중에 다시 시도하십시오.',
			'ACTIVATION_NOT_CONFIRMED' => '이중 인증 활성화를 확인할 수 없습니다. 나중에 다시 시도하십시오.',
			'METHOD_ALREADY_ENABLED' => '메서드가 이미 활성화되었습니다.',
			'VERIFICATION_CODE_ALREADY_SENT' => '코드가 이미 전송되었습니다.',
			'UNKNOWN_ERROR' => '알 수 없는 오류입니다. 나중에 다시 시도하십시오.',
			'INVALID_VERIFICATION_CODE' => '확인 코드가 유효하지 않습니다.',
			'ACTIVATION_PROCESS_NOT_FOUND' => '활성화 확인 프로세스가 만료되었습니다. 다시 코드를 요청해주세요.',
		],

		'disable_two_factor_auth_status_codes' => [
			'NO_METHODS_ENABLED' => '이중 인증이 활성화되지 않았습니다.',
			'TWO_FACTOR_DISABLED' => '이중 인증이 비활성화되었습니다.',
			'VERIFICATION_PROCESS_NOT_FOUND' => '비활성화 확인 프로세스가 만료되었습니다. 다시 코드를 요청해주세요.',
			'METHOD_NOT_ENABLED' => '선택된 이중 인증 방법이 활성화되지 않았습니다.',
			'INVALID_VERIFICATION_CODE' => '확인 코드가 유효하지 않습니다.',
			'UNKNOWN_ERROR' => '알 수 없는 오류입니다. 나중에 다시 시도하십시오.',
		],

		'two_factor_auth_enabled' => '2FA 활성화됨',
		'two_factor_auth_add_method' => '확인 방법 추가',
		'two_factor_auth_disable' => '2FA 비활성화',
		'two_factor_auth_method_empty' => '메서드가 제공되지 않음',
		'two_factor_auth_code_empty' => '코드가 제공되지 않음',

    ),
);