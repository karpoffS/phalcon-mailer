<?php
namespace Mailer\Controllers;

use Mailer\Models\Profiles;
use Mailer\Models\Permissions;

/**
 * Просмотр и определение разрешения для различных уровней профиля.
 */
class PermissionsController extends ControllerBase
{

    public function initialize() {
        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    /**
     * Просмотр разрешений на уровне профиля, и изменить их, если у нас есть POST.
     */
    public function indexAction()
    {

        if ($this->request->isPost()) {

            // Проверка профиля
            $profile = Profiles::findFirstById($this->request->getPost('profileId'));

            if ($profile) {

                if ($this->request->hasPost('permissions')) {

                    // Удаляем текущие разрешения
                    $profile->getPermissions()->delete();

                    // Сохраняем новые разрешения
                    foreach ($this->request->getPost('permissions') as $permission) {

                        $parts = explode('.', $permission);

                        $permission = new Permissions();
                        $permission->profilesId = $profile->id;
                        $permission->resource = $parts[0];
                        $permission->action = $parts[1];

                        $permission->save();
                    }

                    $this->flash->success('Разрешения были обновлены успешно!');
                }

                // Перестройка ACL
                $this->acl->rebuild();

                // Передаём текущие разрешения Вьюшке
                $this->view->permissions = $this->acl->getPermissions($profile);
            }

            // Перредаём новый профиль вьюшке
            $this->view->profile = $profile;
        }

        // Изменяем разрешения на всех актывных профилях
        $this->view->setVar('profiles', Profiles::find('active = 1 AND hiding = 0'));
    }
}
