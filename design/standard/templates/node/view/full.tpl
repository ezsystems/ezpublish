{default with_children=true()
         is_editable=true()
	 is_standalone=true()}
{let page_limit=25
     list_count=and($with_children,fetch('content','list_count',hash(parent_node_id,$node.node_id)))}
{default content_object=$node.object
         content_version=$node.contentobject_version_object
         node_name=$node.name}

{section show=$is_standalone}
<form method="post" action={"content/action"|ezurl}>
{/section}

<table cellspacing="5" cellpadding="0" border="0">
<tr>
	<td>
{*	{$node.name|texttoimage('archtura')}  *}
 	<div class="maincontentheader">
        <h1>{$node_name}</h1>
        </div>
	<input type="hidden" name="TopLevelNode" value="{$content_object.main_node_id}" />
	</td>
</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td valign="top">

    {section name=ContentObjectAttribute loop=$content_version.contentobject_attributes}
    <div class="block">
        <label>{$ContentObjectAttribute:item.contentclass_attribute.name}:</label>
	{switch match=$ContentObjectAttribute:item.is_a}
	{case match=ezstring}
    	<p class="box">{attribute_view_gui attribute=$ContentObjectAttribute:item}</p>
	{/case}
	{case}
    	<p class="box">{attribute_view_gui attribute=$ContentObjectAttribute:item}</p>
	{/case}
	{/switch}
    </div>
    {/section}

    </td>
    <td width="120" valign="top">

    {let name=Object  related_objects=$content_version.related_contentobject_array}

      {section name=ContentObject  loop=$Object:related_objects show=$Object:related_objects  sequence=array(bglight,bgdark)}

        <div class="block">
        {content_view_gui view=text_linked content_object=$Object:ContentObject:item}
        </div>
    
      {section-else}
      {/section}
    {/let}

    {section show=$is_standalone}
      {section name=ContentAction loop=$content_object.content_action_list show=$content_object.content_action_list}
      <div class="block">
      <input type="submit" name="{$ContentAction:item.action}" value="{$ContentAction:item.name|i18n('design/standard/node/view')}" />
      </div>
      {/section}
    {/section}
    </td>
</tr>
</table>

{section show=$is_editable}
   {switch match=$content_object.can_edit}
   {case match=1}
   <input type="hidden" name="ContentObjectID" value="{$content_object.id}" />
   <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n('design/standard/node/view')}" />
   {/case}
   {case match=0}
   {/case}
   {/switch}
{/section}


{section show=$with_children}

{let children=fetch('content','list',hash(parent_node_id,$node.node_id,sort_by,$node.sort_array,limit,$page_limit,offset,$view_parameters.offset))}

{section show=$children}

<hr noshade="noshade" />

<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <th>
    {"Name:"|i18n("design/standard/node/view")}
    </th>
    <th>
    {"Class:"|i18n("design/standard/node/view")}
    </th>
    <th>
    {"Sorting:"|i18n("design/standard/node/view")}
    </th>
    <th colspan="2" align="right">
    {"Remove:"|i18n("design/standard/node/view")}
    </th>
</tr>
{section name=Child loop=$children  sequence=array(bglight,bgdark)}
<tr>
	<td class="{$Child:sequence}">
        <a href={concat('content/view/full/',$Child:item.node_id)|ezurl}>{node_view_gui view=line content_node=$Child:item}</a>
	</td>
        <td class="{$Child:sequence}">{$Child:item.object.class_name}
	</td>
	{section show=eq($node.sort_array[0][0],'priority')}
	<td width="40" align="left" class="{$Child:sequence}">
	  <input type="text" name="Priority[]" size="2" value="{$Child:item.priority}">
          <input type="hidden" name="PriorityID[]" value="{$Child:item.node_id}">
	</td>
	{/section}
	{switch name=sw match=$Child:item.object.can_edit}
        {case match=1}
	<td width="1%" class="{$Child:sequence}">
        <a href={concat("content/edit/",$Child:item.contentobject_id)|ezurl}><img src={"edit.png"|ezimage} alt="Edit" border="0" /></a>
        </td>
	{/case}
        {case} 
	<td class="{$Child:sequence}" width="1%">
	</td>
        {/case}
        {/switch}
	{switch name=sw match=$Child:item.object.can_remove}
        {case match=1}
	<td class="{$Child:sequence}" align="right" width="1%">
             <input type="checkbox" name="DeleteIDArray[]" value="{$Child:item.node_id}" />
	</td>
	{/case}
        {case} 
	<td class="{$Child:sequence}" align="right" width="1%">
	</td>
	<td width="1%" class="{$Child:sequence}"></td>
        {/case}
        {/switch} 
</tr>
{/section}
<tr>
    <td>
    </td>
    <td>
    </td>
    <td>
    {switch match=$content_object.can_edit}
        {case match=1}
        {section show=eq($node.sort_array[0][0],'priority')}
         <input class="button" type="submit"  name="UpdatePriorityButton" value="{'Update'|i18n('design/standard/node/view')}" />
        {/section}
        {/case}
        {case match=0}
        {/case}
    {/switch}
    </td>
    <td colspan="2" align="right">
    {section show=fetch('content','list',hash(parent_node_id,$node.node_id,sort_by,$node.sort_array,limit,$page_limit,offset,$view_parameters.offset))}
    <input class="button" type="submit" name="RemoveButton" value="{'Remove'|i18n('design/standard/node/view')}" />
    {/section}
    </td>
</tr>
</table>

{/section}
{/let}

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/content/view','/full/',$node.node_id)
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$page_limit}

<div class="buttonblock">

{switch match=$content_object.can_create}
{case match=1}
         <input type="hidden" name="NodeID" value="{$node.node_id}" />
         <select name="ClassID">
	      {section name=Classes loop=$content_object.can_create_class_list}
	      <option value="{$Classes:item.id}">{$Classes:item.name}</option>
	      {/section}
         </select>
         <input class="button" type="submit" name="NewButton" value="{'New'|i18n('design/standard/node/view')}" />
{/case}
{case match=0}

{/case}
{/switch}

<input type="hidden" name="ContentObjectID" value="{$content_object.id}" />

</div>

{/section}


{section show=$is_standalone}
</form>
{/section}

{/default}
{/let}
{/default}
