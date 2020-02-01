
## next (v1.0.0)

## v0.9.2

### Changes

- [#9](https://github.com/litphp/litphp/pull/9) air: container exception happened during setter injection is no longer ignored
- [#10](https://github.com/litphp/litphp/pull/10) air/bolt: move \Lit\Bolt\Router\BoltContainerStub to \Lit\Air\ContainerStub, the old BoltContainerStub is now deprecated
- [#11](https://github.com/litphp/litphp/pull/11) bolt: mark EventsHub as deprecated
- Misc: apply PSR-12 code style

## v0.9.1

### Changes

- [#8](https://github.com/litphp/litphp/pull/8) nimo: MiddlewarePipe refactored
  - Behavior of middleware calling `$next` multiple times is changed (to be meaningful), [related documentation here](http://litphp.github.io/docs/nimo#next-passed-to-middleware)
- [#7](https://github.com/litphp/litphp/pull/7) Added docblocks to all class / public method.
  - Some method signature are changed (mostly adding types), should not affect anyone unless he subclass and override any of them. Detailed list can be found [here](https://github.com/litphp/litphp/pull/7)
