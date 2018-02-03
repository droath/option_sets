# Option Sets

The option sets module provides an interface for implementing and managing multiple option sets. Option sets are a grouping of options that can be shared among numerous fields through the Drupal entity ecosystem.

Once an entity starts utilizing an option sets field type, only new options can be added. Existing option keys are unable to be removed or changed until all dependencies have been exonerated from the system.

## Integrations 

#### [Pattern Library](https://github.com/droath/pattern_library)
A pattern library modifier is exposed via the plugin system. It allows developers to define specific option sets as the modifier type when creating library pattern.

```
modifiers:
  color:
    type: option_sets
    option_sets_id: colors
```
