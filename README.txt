##Purpose/scope
This is meant to be a general, easy/simple language parser to be included where necessary.

Mode 0: replaces {$key} with $value
Mode 1: replaces [$key] with $value
Mode 2: replaces {$key} and/or [$key] with $value
- Note: mode 2 calls mode 1 before mode 0, so {key_with_[other]} will first replace [other] and then replace for {key_with_othervalue}.
- This means that you can have some really crazy variable variability madness going on, if you really want to.
Mode 3: replaces $key with $value or [$key_with_spaces] with $value
- Note: mode 3 is more of a rough translator than a language parser.

If want to go ham, you can call mode 2 and put that output into mode 3 for an extensible translator.

If no value for each key can be found, it will simply return the key. All brackets are removed.
However, if the beginning bracket is escaped (i.e. "\[something]"), it removes the "\" and leaves the brackets and bracketed contents.

The following languages are supported:

[PHP]
ELPLanguage class handles the instantiation of languages from a text language file.
One class file; no external depedencencies.
Example:
:: $language = new ELPLanguage;
:: $parser = new ELPParser;
:: $language->setLanguage ("shop", $assoc_array);
:: $language->setLanguageFromFile ("languages/shop.txt");
:: $parser->addLanguage($language);
:: echo $parser->parse("<h3>{item1name}</h3><p>Description: {item1description}</p>", "english");
etc
