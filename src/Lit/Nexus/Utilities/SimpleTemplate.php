<?php

declare(strict_types=1);

namespace Lit\Nexus\Utilities;

class SimpleTemplate
{
    protected $tag_re = '#`([^`\r\n]+)(?:[\r\n`])#';
    protected $pre = [
        '<?' => '!!TPL_PHP!!',
    ];
    protected $post = [
        '!!TPL_PHP!!' => '<?=\'<\'?>?',
    ];
    protected $rule = array(
        'if' => '<?php if(%0):?>',
        'else' => '<?php else:?>',
        'elif' => '<?php elseif (%0):?>',
        '/if' => '<?php endif;?>',
        'loop' => '<?php if(is_array(%1)||%1 instanceof Traversable)foreach(%1 as %2 => %3):?>',
        '/loop' => '<?php endforeach;?>',
        'php' => "<?php %0",
        '/php' => '?>',
    );
    protected $code = null;
    protected $compiledCode;

    /**
     * @param string $templateCode
     */
    protected function __construct(string $templateCode)
    {
        $this->code = $templateCode;
    }

    public static function instance(string $templateCode)
    {
        return new static($templateCode);
    }

    public function render($data)
    {
        ob_start();
        extract($data);
        eval('?>' . $this->compile());
        return ob_get_clean();
    }

    public function compile()
    {
        if (!isset($this->compiledCode)) {
            $templateCode = $this->code;

            $templateCode = strtr($templateCode, $this->pre);
            $templateCode = preg_replace_callback($this->tag_re, [$this, 'resolve'], $templateCode);
            $templateCode = strtr($templateCode, $this->post);

            $this->compiledCode = $templateCode;
        }

        return $this->compiledCode;
    }

    protected function resolve($match)
    {
        list(, $statement) = $match;

        $param = preg_replace('#^\S+\s*#', '', $statement);
        $params = preg_split('#\s+#', $statement);
        assert(!!$params);
        $tag = array_shift($params);

        $result = self::parseShortTag($tag{0}, $statement);
        if ($result !== false) {
            return $result;
        }

        foreach ($this->rule as $t => $rule) {
            if ($t == $tag) {
                $rule = str_replace('%0', $param, $rule);
                $k = 0;
                if (count($params) > 0) {
                    foreach ($params as $p) {
                        $rule = str_replace('%' . ++$k, $p, $rule);
                    }
                }

                return $rule . "\n";
            }
        }

        throw new \Exception('unimplemented');
    }

    protected static function parseShortTag($initial, $statement)
    {
        switch ($initial) {
            case '#':
                return null;
            case '$':
                $arr = explode('|', $statement);
                $statement = array_shift($arr);
                while ($func = array_shift($arr)) {
                    $func = explode(' ', trim($func));
                    if (count($func) == 1) {
                        // `$text|trim`
                        $statement = "{$func[0]}($statement)";
                    } else {
                        //`$timestamp|date 'Y-m-d'`  OR `$text|substr #,0,4`
                        $funcname = array_shift($func);
                        $args = implode(' ', $func);
                        if (false === strpos($args, '#')) {
                            $args .= ',#';
                        }
                        $args = str_replace('#', $statement, $args);
                        $statement = "$funcname($args)";
                    }
                }
                return "<?php echo $statement?>\n";
            case '=':
                $t = substr($statement, 1);
                return "<?php echo $t?>\n";
            case '~':
                $t = substr($statement, 1);
                return "<?php $t?>\n";
            default:
                return false;
        }
    }
}
