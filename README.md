University Library Applications Administrator
=============================================

Some archival tools don't include easy to use interfaces to editing metadata,
so librarians, catalogers, and archivists use spreadsheet tools. When finished
editing, the data needs to manipulated into a form that can be imported.

The data in the ingest directory needs to be converted to the simple archive
format for ingest into a [DSpace](http://dspace.org/) installation. The metadata
is all in file named `data.csv`.

The simple archive format is documented in the [DSpace documentation](https://wiki.duraspace.org/display/DSDOC4x/Importing+and+Exporting+Items+via+Simple+Archive+Format#ImportingandExportingItemsviaSimpleArchiveFormat-DSpaceSimpleArchiveFormat). Fields beginning with dc should
be in the dublin_core.xml file, while thesis fields should end up in metadata_etd.xml.

The cataloger that prepared the data has given us the following column to field mapping:

| Column                                             | Metadata Field                 |
|----------------------------------------------------|--------------------------------|
| Title                                              | dc.title                       |
| Authors (repeating, semicolon delimited)           | dc.contributor.author          |
| Publisher                                          | dc.publisher                   |
| Subject (repeating, multiple fields)               | dc.subject                     |
| Date Issued                                        | dc.date.issued                 |
| Genre                                              | dc.genre                       |
| CollectionID                                       | dc.identifier.collectionId     |
| Description                                        | dc.description                 |
| Abstract Description                               | dc.description.abstract        |
| Advisor                                            | dc.contributor.advisor         |
| Committee Members (repeating, semicolon delimited) | dc.contributor.committeeMember |
| Degree Discipline                                  | thesis.degree.discipline       |
| Degree Level                                       | thesis.degree.level            |
| Files                                              | See Below                      |

In this data set, theses (items with thesis.degree.discipline) should also have the field
`thesis.degree.grantor` set to "College of William and Mary".

The files are located in the ingest/files directory, and are noted in the Files column of
the spreadsheet, and should be included in the final result ready to be imported.

Please write a small tool that will handle this conversion, and share it with us
using your favorite software collaboration tool. It won't bother us
if you share your solution with the world while you're at it - we often openly
collaborate with other libraries.

Be sure to document your solution so that someone familiar with the problem can
use it. We don't care what language you use, as long as it runs on a Linux system.

A tool like this would be used by the applications administrator, so the
interface and complexity should be something you'd like to use and maintain.

If you think we missed something here let us know and we'll update the README file.
# application_programmer
