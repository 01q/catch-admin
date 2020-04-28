<?php
namespace catcher\generate\factory;

use catcher\exceptions\FailedException;
use catcher\generate\template\Model as Template;
use think\facade\Db;

class Model extends Factory
{
    public function done($params)
    {
        // TODO: Implement done() method.
        $template = new Template();

        $extra = $params['extra'];

        $table = $params['table'];

        [$modelName, $namespace] = $this->parseFilename($params['model']);

        if (!$modelName) {
            throw new FailedException('create Model Failed');
        }

        $content = $template->useTrait($extra['soft_delete']) .
                   $template->name($table) .
                   $template->field($this->parseField($table));

        $class = $template->header() .
                 $template->nameSpace($namespace) .
                 $template->uses($extra['soft_delete']) .
                 $template->createModel($modelName, $table);


        $file = $this->getGeneratePath($params['model']);

        file_put_contents($file, str_replace('{CONTENT}', $content, $class));

        if (!file_exists($file)) {
            throw new FailedException('create model failed');
        }

        return true;
    }


    /**
     * parse field
     *
     * @time 2020年04月28日
     * @param $table
     * @return string
     */
    protected function parseField($table)
    {
        $columns = Db::query('show full columns from ' .
            config('database.connections.mysql.prefix') . $table);

        $new = [];
        foreach ($columns as $field) {
            $new[$field['Field']] = $field['Comment'];
        }

        $fields = [];
        foreach ($new as $field => $comment) {
            $fields[] = sprintf("'%s', // %s", $field, $comment);
        }

        return implode("\r\n\t\t", $fields);
    }
}