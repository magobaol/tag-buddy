# DECISIONS.md
Decisions that a future reader, not knowing them, would undo. Each one carries the condition that would make it wrong. Numbers are never reused.

| If you are about to... | Read |
|---|---|
| merge `Tags::add` and `Tags::append`, or make loading a tags file validate uniqueness | D-001 |

## D-001 - Uniqueness is checked on add and replace, not on load
Names and aliases are unique across the whole vocabulary, case-insensitively. `Tags::add` and `Tags::replace` enforce it (`assertUnique`). Loading from a YAML file, `Tags::fromArrayOfTag` and `Tags::clone` go through the private `append` and do not.

**Why:** a file that already contains a collision, edited by hand before the rule existed, must still be readable, searchable and fixable with `app:tags:edit`. If loading validated, a single collision would make every command fail, including the one that could repair it. Covered by `it_should_still_load_a_file_that_already_contains_collisions` in `tests/Model/Tag/TagsUniquenessTest.php`.

**What would make it wrong:** the tags file can no longer be edited by hand, or collisions can no longer get into it.
