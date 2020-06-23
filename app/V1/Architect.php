<?php
/**
 * Architect
 *
 * @package    App\V1
 * @author     Esben Petersen
 * @link       https://github.com/esbenp/architect/blob/master/src/Architect.php
 */

namespace App\V1;

use InvalidArgumentException;
use Illuminate\Support\Collection;

class Architect
{
    private $modeResolvers = [];

    /**
     * Parse a collection using given modes.
     * @param  mixed $data The collection to be parsed
     * @param  array  $modes The modes to be used, format is ['relation' => 'mode']
     * @param  string $key A key to hoist the collection into the root array
     * @return array
     */
    public function parseData($data, array $modes, $key = null)
    {
        $return = [];

        uksort($modes, function ($a, $b) {
            return substr_count($b, '.')-substr_count($a, '.');
        });

        if (Utility::isCollection($data)) {
            $parsed = $this->parseCollection($modes, $data, $return);
        } else {
            $parsed = $this->parseResource($modes, $data, $return);
        }

        if ($key !== null) {
            $return[$key] = $parsed;
        } else {
            if (in_array('sideload', $modes)) {
                throw new InvalidArgumentException('$key cannot be null when ' .
                    'resources are transformed using sideload.');
            }

            $return = $parsed;
        }

        return $return;
    }

    /**
     * Parse a collection using given modes
     * @param  array  $modes
     * @param  mixed $collection
     * @param  array $root
     * @param  string $fullPropertyPath
     * @return mixed
     */
    private function parseCollection(array $modes, $collection, &$root, $fullPropertyPath = '')
    {
        if (is_array($collection)) {
            foreach ($collection as $i => $resource) {
                $collection[$i] = $this->parseResource($modes, $resource, $root, $fullPropertyPath);
            }
        } elseif ($collection instanceof Collection) {
            $collection = $collection->map(function ($resource) use ($modes, &$root, $fullPropertyPath) {
                return $this->parseResource($modes, $resource, $root, $fullPropertyPath);
            });
        }

        return $collection;
    }

    /**
     * Parse a single resource using given modes
     * @param  array  $modes
     * @param  mixed $resource
     * @param  array $root
     * @param  string $fullPropertyPath
     * @return mixed
     */
    private function parseResource(array $modes, &$resource, &$root, $fullPropertyPath = '')
    {
        foreach ($modes as $relation => $mode) {
            $modeResolver = $this->resolveMode($mode);

            $steps = explode('.', $relation);

            // Get the first resource in the relation
            // TODO: Refactor
            $property = array_shift($steps);
            if (is_array($resource)) {
                if ($resource[$property] === null) {
                    continue;
                }

                $object = &$resource[$property];
            } else {
                if ($resource->{$property} === null) {
                    continue;
                }

                $object = &$resource->{$property};
            }

            if (empty($steps)) {
                // This is the deepest level. Resolve it.
                $fullPropertyPath .= $relation;
                $object = $this->modeResolvers[$mode]->resolve($relation, $object, $root, $fullPropertyPath);
            } else {
                // More levels exist in this relation.
                // We want a drill down and resolve the deepest level first.

                $path = implode('.', $steps);
                $modes = [
                    $path => $mode
                ];

                // Add the previous levels to the full path so it can be used
                // to populate the root level properly.
                $fullPropertyPath .= $property . '.';

                if (Utility::isCollection($object)) {
                    $object = $this->parseCollection($modes, $object, $root, $fullPropertyPath);
                } else {
                    $object = $this->parseResource($modes, $object, $root, $fullPropertyPath);
                }
            }

            // Reset the full property path after running a full relation
            $fullPropertyPath = '';
            Utility::setProperty($resource, $property, $object);
        }

        return $resource;
    }

    /**
     * Resolve a mode resolver class if it has not been resolved before
     * @param  string $mode The mode to be resolved
     * @return Optimus\Architect\ModeResolver\ModeResolverInterface
     */
    private function resolveMode($mode)
    {
        if (!isset($this->modeResolers[$mode])) {
            $this->modeResolvers[$mode] = $this->createModeResolver($mode);
        }

        return $this->modeResolvers[$mode];
    }

    /**
     * Instantiate a mode resolver class
     * @param  string $mode [description]
     * @return Optimus\Architect\ModeResolver\ModeResolverInterface
     */
    private function createModeResolver($mode)
    {
        $class = 'Optimus\Architect\ModeResolver\\';
        switch ($mode) {
            default:
            case 'embed':
                $class .= 'EmbedModeResolver';
                break;
            case 'ids':
                $class .= 'IdsModeResolver';
                break;
            case 'sideload':
                $class .= 'SideloadModeResolver';
                break;
        }

        return new $class;
    }
}
