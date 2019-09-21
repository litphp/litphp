
## v0.9.1

### Changes

- [#8](https://github.com/litphp/litphp/pull/8) nimo: MiddlewarePipe refactored
  - Behavior of middleware calling `$next` multiple times is changed (to be meaningful), [related documentation here](http://litphp.github.io/docs/nimo#next-passed-to-middleware)
- [#7](https://github.com/litphp/litphp/pull/7) Added docblocks to all class / public method.
  - Some method signature are changed (mostly adding types), should not affect anyone unless he subclass and override any of them. Detailed list can be found [here](https://github.com/litphp/litphp/pull/7)
